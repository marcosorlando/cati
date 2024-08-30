CREATE TABLE `ws_cv`
(
    `cv_id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
    `cv_full_name`       varchar(155)              DEFAULT NULL,
    `cv_area`    varchar(255)              DEFAULT NULL,
    `cv_photo`       varchar(255)              DEFAULT NULL,
    `cv_pdf`       varchar(255)              DEFAULT NULL,
    `cv_email`       varchar(255)              DEFAULT NULL,
    `cv_created`     timestamp        NULL     DEFAULT NULL,
    `cv_updated`     timestamp        NULL     DEFAULT NULL,
    `cv_status`      int(11)          NOT NULL DEFAULT 0,
    PRIMARY KEY (`cv_id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;
