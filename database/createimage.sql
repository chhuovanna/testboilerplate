use movierating;

CREATE TABLE `image` (  `image_id` int(10) NOT NULL AUTO_INCREMENT
,`location` text COLLATE utf8mb4_unicode_ci NOT NULL
,  `file_name` text COLLATE utf8mb4_unicode_ci NOT NULL
,`mID` INT
,  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
,`updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
,  PRIMARY KEY (`image_id`)
,  CONSTRAINT  FOREIGN KEY (`mID`) REFERENCES `movie` (`mID`))
 ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
alter table movie add thumbnail_id int;
alter table movie add foreign key (thumbnail_id) references image(image_id);
