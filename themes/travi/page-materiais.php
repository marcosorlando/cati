<?php

    if (!$Read) {
        $Read = new Read;
    }
    $Read->ExeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>

<section class='commonSection casestudysection'>
    <div class='container'>
        <div class='row'>
            <div class='col-xl-12 text-center'>
                <h6 class='sub_title'>Conteúdo para download</h6>
                <h2 class='sec_title with_bar'>
                    <span>Materiais</span>
                </h2>
            </div>
        </div>

        <?php
            $Page = (!empty($URL[2]) ? $URL[2] : 1);
            $Pager = new Pager(BASE . "/materiais/{$page_name}/", "<", ">", 5);
            $Pager->ExePager($Page, 10);

	     /* $Read->FullRead('SELECT DISTINCT(category_name) FROM ' . DB_MATCATEGORIES . ' WHERE seg_status >= :st',
		        'st=1');*/

			$Read->FullRead("SELECT c.category_id, c.category_name, c.category_title, m.mat_title, m.mat_subtitle, m.mat_cover, m.mat_category, m.mat_category_parent, m.mat_link FROM " . DB_MATCATEGORIES . " c, " . DB_MATERIAIS . " m WHERE mat_status = 1 AND mat_date <= NOW() AND mat_category = category_id ORDER BY mat_date DESC LIMIT :limit OFFSET :offset",
                "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");

            if (!$Read->getResult()) {
                $Pager->ReturnPage();
                echo Erro('Ainda Não existem materiais cadastrados na biblioteca. Favor volte mais tarde', E_USER_NOTICE);
            } else {
                ?>
                <div class='row'>
                    <div class='col-xl-12'>
                        <div class='filterMenu'>
                            <ul class='text-right clearfix shafful_filter'>
                                <li class='all' data-group='all'>
                                    <i class='fal fa-bars'></i>Todos
                                </li>

	                            <?php
		                            if ($Read->getResult()):
			                            foreach ($Read->getResult() as $Lista):
				                            extract($Lista);
				                            $CatList[] = $Lista['category_title'];
			                            endforeach;
			                            $CatList = array_unique(
				                            $CatList,
				                            SORT_REGULAR
			                            );

			                            foreach ($CatList as $List => $Key):
				                            $KeyClass = Check::Name($Key);
				                            echo "<li data-group='{$KeyClass}'>
                                            <i class='fal fa-filter'></i>{$Key}
                                        </li>";
			                            endforeach;
		                            endif;
	                            ?>

                            </ul>
                            <!-- end filter navigation -->
                        </div>
                    </div>
                </div>

                <!-- start filter content -->
                <div class='row' id='shafulls'>
                    <?php
                        foreach ($Read->getResult() as $Mat) {
                            extract($Mat);
                            ?>

                            <!-- start portfolio-item item -->
                            <div class='col-xl-4 col-md-6 col-lg-4 shaf_itme'
                                 data-groups='["all", "<?= $category_name; ?>"]'>
                                <div class='singlefolio'>
                                    <img src='<?= BASE; ?>/tim.php?src=/uploads/<?=$mat_cover;?>&w=400&h=250'
                                         alt='<?=$mat_title;?>'/>
                                    <div class='folioHover'>
                                        <p class="text-center">
                                            <a class='download-icon' target='_blank' href='<?= $mat_link; ?>'>
                                                <i class='fal fa-download fa-4x animated fadeInDown infinite'></i>
                                            </a>
                                        </p>
                                        <p>
                                            <a class='cate' target='_blank' href='<?= $mat_link; ?>'><?= $category_title; ?></a>
                                        </p>
                                        <h4>
                                            <a target='_blank' href='<?= $mat_link; ?>'><?=$mat_title;?></a>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <!-- end portfolio item -->

                            <?php
                        }

                        $Pager->ExePaginator(DB_MATERIAIS,
                            "WHERE mat_status = 1 AND mat_date <= NOW() AND (mat_category = :ct OR FIND_IN_SET(:ct, mat_category_parent))",
                            "ct={$page_id}");
                        echo $Pager->getPaginator();
                    ?>
                    <!-- end portfolio item -->
                    </ul>
                </div>
                <!--aqui-->
                <?php
            } ?>
    </div>
</section>
