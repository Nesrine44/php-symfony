-- VERSION NBM

INSERT INTO `portfolio_profile` (id, title, css_class) VALUES (6, "New Business Acceleration", "new_business_acceleration");

INSERT INTO `type` (id, title, css_class) VALUES (5, "New business model", "new_business_model");
UPDATE innovation SET type_id = 5 WHERE classification_id = 3;


ALTER TABLE innovation ADD new_business_opportunity VARCHAR(255) DEFAULT NULL, ADD investment_model VARCHAR(255) DEFAULT NULL, ADD as_seperate_pl TINYINT(1) NOT NULL, ADD idea_description TEXT DEFAULT NULL, ADD strategic_intent_mission TEXT DEFAULT NULL;

-- cities
CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, geoname_id INT DEFAULT NULL, continent_code VARCHAR(255) DEFAULT NULL, continent_name VARCHAR(255) DEFAULT NULL, country_iso_code VARCHAR(255) DEFAULT NULL, country_name VARCHAR(255) DEFAULT NULL, city_name VARCHAR(255) DEFAULT NULL, time_zone VARCHAR(255) DEFAULT NULL, is_in_european_union TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;
ALTER TABLE city ADD picture_url VARCHAR(510) DEFAULT NULL;
CREATE TABLE innovation_city (innovation_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_B90FA7F6948007BF (innovation_id), INDEX IDX_B90FA7F68BAC62AF (city_id), PRIMARY KEY(innovation_id, city_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;
ALTER TABLE innovation_city ADD CONSTRAINT FK_B90FA7F6948007BF FOREIGN KEY (innovation_id) REFERENCES innovation (id) ON DELETE CASCADE;
ALTER TABLE innovation_city ADD CONSTRAINT FK_B90FA7F68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE;

-- DELETE FROM city WHERE id NOT IN (SELECT * FROM (SELECT MIN(c.id) FROM city c GROUP BY c.city_name, c.country_name) x);


-- canvas
CREATE TABLE canvas (id INT AUTO_INCREMENT NOT NULL, innovation_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, block_1_a TEXT DEFAULT NULL, block_1_b TEXT DEFAULT NULL, block_1_c TEXT DEFAULT NULL, block_2_a TEXT DEFAULT NULL, block_2_b TEXT DEFAULT NULL, block_2_c TEXT DEFAULT NULL, block_3_a TEXT DEFAULT NULL, block_3_b TEXT DEFAULT NULL, block_3_c TEXT DEFAULT NULL, block_4_a TEXT DEFAULT NULL, block_4_b TEXT DEFAULT NULL, INDEX IDX_A59F6C18948007BF (innovation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;
ALTER TABLE canvas ADD CONSTRAINT FK_A59F6C18948007BF FOREIGN KEY (innovation_id) REFERENCES innovation (id);

-- open_question
CREATE TABLE open_question (id INT AUTO_INCREMENT NOT NULL, contact_id INT DEFAULT NULL, innovation_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, message TEXT DEFAULT NULL, INDEX IDX_922EDE24E7A1254A (contact_id), UNIQUE INDEX UNIQ_922EDE24948007BF (innovation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;
ALTER TABLE open_question ADD CONSTRAINT FK_922EDE24E7A1254A FOREIGN KEY (contact_id) REFERENCES pr_user (id);
ALTER TABLE open_question ADD CONSTRAINT FK_922EDE24948007BF FOREIGN KEY (innovation_id) REFERENCES innovation (id);
ALTER TABLE innovation ADD open_question_id INT DEFAULT NULL;
ALTER TABLE innovation ADD CONSTRAINT FK_705BDF0D9A68CEC0 FOREIGN KEY (open_question_id) REFERENCES open_question (id);
CREATE UNIQUE INDEX UNIQ_705BDF0D9A68CEC0 ON innovation (open_question_id);

-- financial
ALTER TABLE innovation ADD full_time_employees TEXT DEFAULT NULL, ADD external_text TEXT DEFAULT NULL, ADD project_owner_disponibility TEXT DEFAULT NULL;

