-- VERSION 6.1

ALTER TABLE pr_user ADD accept_scheduled_emails TINYINT(1) NOT NULL;
UPDATE pr_user SET accept_scheduled_emails = 1;

-- VERSION 6.2 LOT 1

ALTER TABLE pr_user CHANGE is_newsletter_accepted accept_newsletter TINYINT(1) NOT NULL;
ALTER TABLE pr_user ADD accept_contact TINYINT(1) NOT NULL;
UPDATE pr_user SET accept_contact = 1;


CREATE TABLE user_entity (id INT AUTO_INCREMENT NOT NULL, pr_title VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;
ALTER TABLE pr_user ADD user_entity_id INT DEFAULT NULL, ADD country VARCHAR(255) DEFAULT NULL, CHANGE title_and_country situation VARCHAR(255) DEFAULT NULL;
ALTER TABLE pr_user ADD CONSTRAINT FK_217AF2D081C5F0B9 FOREIGN KEY (user_entity_id) REFERENCES user_entity (id);
CREATE INDEX IDX_217AF2D081C5F0B9 ON pr_user (user_entity_id);

INSERT INTO `user_entity` VALUES
(1,'Pernod Ricard USA','PR USA'),
(2,'Pernod Ricard Nigeria','PR Nigeria'),
(3,'Pernod Ricard HQ','PR HQ'),
(4,'Irish Distillers','IDL'),
(5,'Pernod Ricard Sub Saharan Africa','PR SSA'),
(6,'Pernod Ricard Colombia','PR Colombia'),
(7,'Pernod Ricard Winemakers - Headquarters','PR Winemakers '),
(8,'Chivas','CBL'),
(9,'The Absolut Company','TAC'),
(10,'Pernod Ricard Sweden','PR Sweden'),
(11,'Pernod Ricard Hong Kong & Macau','PR Hong Kong'),
(12,'Pernod Ricard Italia','PR Italia'),
(13,'Pernod Ricard Espana','PR Espana'),
(14,'Pernod Ricard Winemakers USA','PR Winemakers'),
(15,'Pernod Ricard Mexico','PR Mexico'),
(16,'Pernod Ricard Chile','PR Chile'),
(17,'Pernod Ricard Peru','PR Peru'),
(18,'HCI Paris','HCI'),
(19,'Martell & Co','MMPJ'),
(20,'Corby Spirit and Wine','Corby Spirit and Wine'),
(21,'Pernod Ricard Brasil','PR Brasil'),
(22,'RICARD','Ricard'),
(23,'Pernod Ricard Winemakers Spain','PR Winemakers Spain'),
(24,'Pernod Ricard UK','PR UK'),
(25,'Pernod Ricard Slovenia','PR Slovenia'),
(26,'Pernod Ricard Uruguay','PR Uruguay'),
(27,'Pernod Ricard Swiss','PR Swiss'),
(28,'Pernod Ricard Deutschland','PR Deutschland'),
(29,'Pernod Ricard Austria','PR Austria'),
(30,'Pernod Ricard Australia','PR Australia'),
(31,'Pernod Ricard Global Travel Retail','PR GTR'),
(32,'Pernod Ricard Winemakers New Zealand','PR Winemakers'),
(33,'Pernod Ricard Rouss','PR Rouss'),
(34,'Pernod Ricard Minsk','PR Belarus'),
(35,'Pernod Ricard Croatia','PR Croatia'),
(36,'Pernod Ricard India','PR India'),
(37,'PERNOD','Pernod'),
(38,'Pernod Ricard Asia HQ','PR Asia'),
(39,'Pernod Ricard Gulf','PR Gulf'),
(40,'GH Mumm','MMPJ'),
(41,'Yerevan Brandy Company','Yerevan Brandy Company'),
(42,'Pernod Ricard Europe Middle East & Africa','PR EMEA'),
(43,'Ungava Spirits','Corby Spirit and Wine'),
(44,'Pernod Ricard Middle East & North Africa','PR MENA'),
(45,'Perrier-Jouet','MMPJ'),
(46,'Pernod Ricard Travel Retail Americas','PR GTR'),
(47,'Pernod Ricard Denmark','PR Denmark'),
(48,'Pernod Ricard New Zealand','PR New Zealand'),
(49,'Pernod Ricard Travel Retail Asia Pacific','PR GTR'),
(50,'Pernod Ricard Hellas','PR Hellas'),
(51,'Pernod Ricard Argentina','PR Argentina'),
(52,'Pernod Ricard Winemakers Australia','PR Winemakers'),
(53,'Pernod Ricard Thailand','PR Thailand'),
(54,'Jan Becher','Jan Becher'),
(55,'Pernod Ricard South Africa','PR South Africa'),
(56,'Pernod Ricard Portugal','PR Portugal'),
(57,'Pernod Ricard Ukraine','PR Ukraine'),
(58,'Pernod Ricard Bulgaria','PR Bulgaria'),
(59,'Pernod Ricard Norway','PR Norway'),
(60,'Pernod Ricard Estonia','PR Estonia'),
(61,'The Absolut Company (TAC)','TAC'),
(62,'Pernod Ricard Nederland','PR Nederland'),
(63,'Hiram Walker & Sons','Corby Spirit and Wine'),
(64,'Pernod Ricard Belgium','PR Belgium'),
(65,'PR Sweden','PR Sweden'),
(66,'Pernod Ricard West Africa','PR West Africa'),
(67,'Wyborowa','Wyborowa'),
(68,'Pernod Ricard Korea','PR Korea'),
(69,'Pernod Ricard Venezuela','PR Venezuela'),
(70,'Pernod Ricard China','PR China'),
(71,'Pernod Ricard Singapore','PR Singapore'),
(72,'Pernod Ricard Malaysia','PR Malaysia'),
(73,'The Absolut Company - USA','PR USA'),
(74,'Pernod Ricard Taiwan','PR Taiwan'),
(75,'Pernod Ricard East Africa','PR East Africa'),
(76,'Pernod Ricard Finland','PR Finland'),
(77,'Pernod Ricard Slovakia','PR Slovakia'),
(79,'Pernod Ricard Latvia','PR Latvia'),
(80,'HCI Cuba','HCI'),
(81,'Pernod Ricard Lanka','PR Lanka'),
(82,'Pernod Ricard Japan','PR Japan'),
(83,'Pernod Ricard Hungary','PR Hungary'),
(84,'Pernod Ricard Turkey','PR Turkey'),
(85,'Pernod Ricard Vietnam','PR Vietnam'),
(86,'Pernod Ricard Romania','PR Romania'),
(87,'Pernod Ricard Kazakhstan','PR Kazakhstan'),
(88,'Black Forest Distillers','Black Forest Distillers'),
(89,'Pernod Ricard Maroc','PR Maroc'),
(90,'Pernod Ricard Serbia','PR Serbia'),
(91,'Pernod Ricard Travel Retail EMEA','PR GTR');



UPDATE pr_user SET user_entity_id = 1 WHERE id = 1;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 8;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 40;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 56;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 84;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 123;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 129;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 130;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 133;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 134;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 153;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 163;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 167;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 184;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 188;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 200;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 212;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 215;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 271;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 278;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 279;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 307;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 358;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 359;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 364;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 416;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 417;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 418;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 422;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 427;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 432;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 441;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 446;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 447;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 450;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 458;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 474;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 476;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 501;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 506;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 508;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 519;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 536;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 541;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 629;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 639;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 640;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 641;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 643;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 653;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 681;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 707;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 716;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 764;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 790;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 809;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 814;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 823;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 832;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 873;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 877;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 879;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 888;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 890;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 901;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 905;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 911;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 927;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 928;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 963;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1048;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1089;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1116;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1118;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1128;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1143;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1166;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1195;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1224;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1235;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1260;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1263;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1278;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1279;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1305;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1337;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1381;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1390;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1391;
UPDATE pr_user SET user_entity_id = 1 WHERE id = 1394;
UPDATE pr_user SET user_entity_id = 2 WHERE id = 1179;
UPDATE pr_user SET user_entity_id = 2 WHERE id = 1397;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 5;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 14;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 19;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 33;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 42;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 49;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 68;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 87;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 100;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 112;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 113;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 136;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 144;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 160;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 161;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 168;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 173;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 196;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 216;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 224;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 227;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 238;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 247;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 256;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 267;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 272;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 284;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 286;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 296;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 305;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 326;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 350;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 363;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 367;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 370;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 394;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 410;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 423;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 438;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 455;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 470;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 473;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 477;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 526;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 546;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 557;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 559;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 566;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 572;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 582;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 591;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 602;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 633;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 666;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 679;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 680;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 698;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 700;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 706;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 709;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 721;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 734;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 766;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 768;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 782;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 817;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 827;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 830;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 857;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 864;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 865;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 880;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 883;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 889;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 908;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 926;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 933;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 969;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 985;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 987;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1026;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1027;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1035;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1036;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1053;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1065;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1067;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1073;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1122;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1136;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1165;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1169;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1186;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1200;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1202;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1227;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1238;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1239;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1240;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1256;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1258;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1265;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1267;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1283;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1302;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1307;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1312;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1313;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1325;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1341;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1342;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1348;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1349;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1350;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1356;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1363;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1370;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1375;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1378;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1383;
UPDATE pr_user SET user_entity_id = 3 WHERE id = 1386;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 9;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 12;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 13;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 70;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 72;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 86;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 125;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 131;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 186;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 197;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 198;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 202;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 230;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 262;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 292;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 294;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 353;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 403;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 428;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 443;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 448;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 500;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 521;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 544;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 547;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 567;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 568;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 630;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 645;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 649;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 715;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 752;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 761;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 802;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 811;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 812;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 820;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 826;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 848;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 858;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 884;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 895;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 913;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 930;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 953;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 975;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1040;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1126;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1130;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1209;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1225;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1251;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1257;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1309;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1331;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1385;
UPDATE pr_user SET user_entity_id = 4 WHERE id = 1389;
UPDATE pr_user SET user_entity_id = 5 WHERE id = 631;
UPDATE pr_user SET user_entity_id = 5 WHERE id = 902;
UPDATE pr_user SET user_entity_id = 5 WHERE id = 1059;
UPDATE pr_user SET user_entity_id = 5 WHERE id = 1132;
UPDATE pr_user SET user_entity_id = 5 WHERE id = 1145;
UPDATE pr_user SET user_entity_id = 5 WHERE id = 1304;
UPDATE pr_user SET user_entity_id = 5 WHERE id = 1371;
UPDATE pr_user SET user_entity_id = 6 WHERE id = 2;
UPDATE pr_user SET user_entity_id = 6 WHERE id = 51;
UPDATE pr_user SET user_entity_id = 6 WHERE id = 235;
UPDATE pr_user SET user_entity_id = 6 WHERE id = 464;
UPDATE pr_user SET user_entity_id = 6 WHERE id = 1046;
UPDATE pr_user SET user_entity_id = 6 WHERE id = 1271;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 64;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 137;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 193;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 222;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 233;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 360;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 383;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 497;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 499;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 539;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 542;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 571;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 603;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 664;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 671;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 726;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 794;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 822;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 991;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1009;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1011;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1028;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1083;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1108;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1114;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1138;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1139;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1147;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1170;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1173;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1187;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1215;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1232;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1280;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1281;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1284;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1285;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1286;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1288;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1290;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1360;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1376;
UPDATE pr_user SET user_entity_id = 7 WHERE id = 1387;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 3;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 11;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 15;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 27;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 31;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 43;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 66;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 67;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 75;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 99;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 128;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 139;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 166;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 170;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 189;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 195;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 203;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 208;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 249;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 255;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 264;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 265;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 309;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 333;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 334;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 357;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 369;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 387;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 393;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 398;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 406;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 409;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 433;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 442;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 456;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 467;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 487;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 514;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 520;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 527;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 551;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 562;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 569;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 589;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 594;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 619;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 658;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 670;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 718;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 757;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 758;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 773;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 774;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 777;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 785;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 798;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 804;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 815;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 831;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 838;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 840;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 846;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 850;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 881;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 915;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 917;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 923;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 929;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 959;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 977;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 999;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1055;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1057;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1063;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1076;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1105;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1106;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1112;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1142;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1189;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1210;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1222;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1229;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1255;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1274;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1299;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1311;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1317;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1359;
UPDATE pr_user SET user_entity_id = 8 WHERE id = 1367;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 6;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 10;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 71;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 73;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 94;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 138;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 140;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 146;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 151;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 155;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 187;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 228;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 254;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 261;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 275;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 313;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 314;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 318;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 348;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 362;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 375;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 401;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 407;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 408;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 445;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 489;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 492;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 513;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 535;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 553;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 573;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 578;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 579;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 600;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 605;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 610;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 635;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 683;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 728;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 745;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 750;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 765;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 784;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 797;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 803;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 833;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 854;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 876;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 894;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 919;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 939;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 941;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 957;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 979;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 993;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1021;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1062;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1069;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1107;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1164;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1194;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1211;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1292;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1306;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1328;
UPDATE pr_user SET user_entity_id = 9 WHERE id = 1393;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 7;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 114;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 688;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 695;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 723;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 755;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 1155;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 1167;
UPDATE pr_user SET user_entity_id = 10 WHERE id = 1384;
UPDATE pr_user SET user_entity_id = 11 WHERE id = 126;
UPDATE pr_user SET user_entity_id = 11 WHERE id = 1162;
UPDATE pr_user SET user_entity_id = 11 WHERE id = 1171;
UPDATE pr_user SET user_entity_id = 11 WHERE id = 1196;
UPDATE pr_user SET user_entity_id = 11 WHERE id = 1197;
UPDATE pr_user SET user_entity_id = 11 WHERE id = 1372;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 16;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 23;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 298;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 330;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 523;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 524;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 592;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 772;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 947;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 1042;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 1043;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 1199;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 1344;
UPDATE pr_user SET user_entity_id = 12 WHERE id = 1362;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 17;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 18;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 20;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 90;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 119;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 147;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 176;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 301;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 302;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 389;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 454;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 459;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 463;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 595;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 620;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 771;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 792;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 835;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 893;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 897;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 903;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 918;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 1261;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 1264;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 1269;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 1270;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 1272;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 1275;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 1334;
UPDATE pr_user SET user_entity_id = 13 WHERE id = 1353;
UPDATE pr_user SET user_entity_id = 14 WHERE id = 236;
UPDATE pr_user SET user_entity_id = 14 WHERE id = 434;
UPDATE pr_user SET user_entity_id = 14 WHERE id = 545;
UPDATE pr_user SET user_entity_id = 14 WHERE id = 1250;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 21;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 47;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 91;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 117;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 120;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 148;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 159;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 190;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 210;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 220;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 237;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 273;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 322;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 460;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 461;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 1017;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 1019;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 1044;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 1109;
UPDATE pr_user SET user_entity_id = 15 WHERE id = 1249;
UPDATE pr_user SET user_entity_id = 16 WHERE id = 22;
UPDATE pr_user SET user_entity_id = 16 WHERE id = 1282;
UPDATE pr_user SET user_entity_id = 16 WHERE id = 1380;
UPDATE pr_user SET user_entity_id = 17 WHERE id = 217;
UPDATE pr_user SET user_entity_id = 17 WHERE id = 332;
UPDATE pr_user SET user_entity_id = 17 WHERE id = 585;
UPDATE pr_user SET user_entity_id = 17 WHERE id = 737;
UPDATE pr_user SET user_entity_id = 17 WHERE id = 1248;
UPDATE pr_user SET user_entity_id = 17 WHERE id = 1343;
UPDATE pr_user SET user_entity_id = 18 WHERE id = 24;
UPDATE pr_user SET user_entity_id = 18 WHERE id = 741;
UPDATE pr_user SET user_entity_id = 18 WHERE id = 1095;
UPDATE pr_user SET user_entity_id = 18 WHERE id = 1103;
UPDATE pr_user SET user_entity_id = 18 WHERE id = 1277;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 25;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 36;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 50;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 76;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 124;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 135;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 156;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 162;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 209;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 252;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 260;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 270;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 287;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 306;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 315;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 345;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 346;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 377;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 436;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 453;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 468;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 480;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 528;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 560;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 652;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 668;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 676;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 689;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 711;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 736;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 824;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 866;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 914;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 973;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 1058;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 1326;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 1333;
UPDATE pr_user SET user_entity_id = 19 WHERE id = 1352;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 30;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 38;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 80;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 179;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 293;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 320;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 325;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 388;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 451;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 478;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 503;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 512;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 515;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 596;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 628;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 672;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 810;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 849;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 869;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 943;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 1075;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 1214;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 1221;
UPDATE pr_user SET user_entity_id = 20 WHERE id = 1308;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 32;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 555;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 597;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 674;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 747;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 786;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 860;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 900;
UPDATE pr_user SET user_entity_id = 21 WHERE id = 1377;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 37;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 69;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 78;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 81;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 82;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 102;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 110;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 142;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 150;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 169;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 242;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 374;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 382;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 425;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 518;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 561;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 577;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 583;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 606;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 622;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 650;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 702;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 733;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 738;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 742;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 780;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 949;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1061;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1087;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1099;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1120;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1133;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1198;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1223;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1262;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1301;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1310;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1320;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1332;
UPDATE pr_user SET user_entity_id = 22 WHERE id = 1395;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 46;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 372;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 373;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 380;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 402;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 710;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 898;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 1037;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 1183;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 1268;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 1327;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 1340;
UPDATE pr_user SET user_entity_id = 23 WHERE id = 1347;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 149;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 172;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 221;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 288;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 319;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 324;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 371;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 390;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 505;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 550;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 563;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 615;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 616;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 690;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 701;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 730;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 787;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 845;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 885;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 965;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 983;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 1051;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 1184;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 1228;
UPDATE pr_user SET user_entity_id = 24 WHERE id = 1388;
UPDATE pr_user SET user_entity_id = 25 WHERE id = 627;
UPDATE pr_user SET user_entity_id = 25 WHERE id = 806;
UPDATE pr_user SET user_entity_id = 25 WHERE id = 1241;
UPDATE pr_user SET user_entity_id = 25 WHERE id = 1243;
UPDATE pr_user SET user_entity_id = 26 WHERE id = 48;
UPDATE pr_user SET user_entity_id = 27 WHERE id = 55;
UPDATE pr_user SET user_entity_id = 27 WHERE id = 118;
UPDATE pr_user SET user_entity_id = 27 WHERE id = 587;
UPDATE pr_user SET user_entity_id = 27 WHERE id = 1013;
UPDATE pr_user SET user_entity_id = 27 WHERE id = 1192;
UPDATE pr_user SET user_entity_id = 27 WHERE id = 1336;
UPDATE pr_user SET user_entity_id = 27 WHERE id = 1338;
UPDATE pr_user SET user_entity_id = 27 WHERE id = 1358;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 58;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 60;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 121;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 327;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 440;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 685;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 731;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 791;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 856;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 868;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1038;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1091;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1161;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1163;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1216;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1254;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1259;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1266;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1273;
UPDATE pr_user SET user_entity_id = 28 WHERE id = 1298;
UPDATE pr_user SET user_entity_id = 29 WHERE id = 59;
UPDATE pr_user SET user_entity_id = 29 WHERE id = 183;
UPDATE pr_user SET user_entity_id = 29 WHERE id = 338;
UPDATE pr_user SET user_entity_id = 29 WHERE id = 836;
UPDATE pr_user SET user_entity_id = 29 WHERE id = 1297;
UPDATE pr_user SET user_entity_id = 29 WHERE id = 1330;
UPDATE pr_user SET user_entity_id = 29 WHERE id = 1361;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 63;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 178;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 268;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 277;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 549;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 646;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 916;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 1148;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 1149;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 1154;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 1246;
UPDATE pr_user SET user_entity_id = 30 WHERE id = 1392;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 61;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 207;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 266;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 291;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 342;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 517;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 661;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 1315;
UPDATE pr_user SET user_entity_id = 31 WHERE id = 1316;
UPDATE pr_user SET user_entity_id = 32 WHERE id = 62;
UPDATE pr_user SET user_entity_id = 32 WHERE id = 498;
UPDATE pr_user SET user_entity_id = 32 WHERE id = 1322;
UPDATE pr_user SET user_entity_id = 33 WHERE id = 95;
UPDATE pr_user SET user_entity_id = 33 WHERE id = 739;
UPDATE pr_user SET user_entity_id = 33 WHERE id = 1030;
UPDATE pr_user SET user_entity_id = 33 WHERE id = 1303;
UPDATE pr_user SET user_entity_id = 33 WHERE id = 1365;
UPDATE pr_user SET user_entity_id = 34 WHERE id = 65;
UPDATE pr_user SET user_entity_id = 35 WHERE id = 693;
UPDATE pr_user SET user_entity_id = 35 WHERE id = 1071;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 92;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 122;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 164;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 240;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 316;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 343;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 399;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 431;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 457;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 580;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 756;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 760;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 776;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 779;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 807;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 808;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 853;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 1001;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 1005;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 1101;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 1111;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 1172;
UPDATE pr_user SET user_entity_id = 36 WHERE id = 1366;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 77;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 101;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 243;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 269;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 310;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 439;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 471;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 530;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 532;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 581;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 675;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 775;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 805;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 839;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 989;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 1060;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 1082;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 1131;
UPDATE pr_user SET user_entity_id = 37 WHERE id = 1354;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 83;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 421;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 437;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 485;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 632;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 660;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 732;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 887;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 891;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 904;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 910;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 995;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 1080;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 1157;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 1158;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 1159;
UPDATE pr_user SET user_entity_id = 38 WHERE id = 1368;
UPDATE pr_user SET user_entity_id = 39 WHERE id = 85;
UPDATE pr_user SET user_entity_id = 39 WHERE id = 127;
UPDATE pr_user SET user_entity_id = 39 WHERE id = 651;
UPDATE pr_user SET user_entity_id = 39 WHERE id = 759;
UPDATE pr_user SET user_entity_id = 39 WHERE id = 971;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 89;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 105;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 232;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 248;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 263;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 289;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 625;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 634;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 748;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 1208;
UPDATE pr_user SET user_entity_id = 40 WHERE id = 1276;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 93;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 96;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 253;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 378;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 490;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 565;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 859;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 1244;
UPDATE pr_user SET user_entity_id = 41 WHERE id = 1355;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 98;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 106;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 180;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 199;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 206;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 328;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 584;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 598;
UPDATE pr_user SET user_entity_id = 42 WHERE id = 1093;
UPDATE pr_user SET user_entity_id = 43 WHERE id = 104;
UPDATE pr_user SET user_entity_id = 44 WHERE id = 108;
UPDATE pr_user SET user_entity_id = 44 WHERE id = 312;
UPDATE pr_user SET user_entity_id = 44 WHERE id = 932;
UPDATE pr_user SET user_entity_id = 44 WHERE id = 1003;
UPDATE pr_user SET user_entity_id = 44 WHERE id = 1134;
UPDATE pr_user SET user_entity_id = 44 WHERE id = 1190;
UPDATE pr_user SET user_entity_id = 44 WHERE id = 1193;
UPDATE pr_user SET user_entity_id = 45 WHERE id = 141;
UPDATE pr_user SET user_entity_id = 45 WHERE id = 308;
UPDATE pr_user SET user_entity_id = 45 WHERE id = 344;
UPDATE pr_user SET user_entity_id = 45 WHERE id = 361;
UPDATE pr_user SET user_entity_id = 45 WHERE id = 722;
UPDATE pr_user SET user_entity_id = 46 WHERE id = 1015;
UPDATE pr_user SET user_entity_id = 46 WHERE id = 1318;
UPDATE pr_user SET user_entity_id = 46 WHERE id = 1396;
UPDATE pr_user SET user_entity_id = 47 WHERE id = 494;
UPDATE pr_user SET user_entity_id = 47 WHERE id = 1177;
UPDATE pr_user SET user_entity_id = 47 WHERE id = 1178;
UPDATE pr_user SET user_entity_id = 47 WHERE id = 1181;
UPDATE pr_user SET user_entity_id = 48 WHERE id = 354;
UPDATE pr_user SET user_entity_id = 48 WHERE id = 509;
UPDATE pr_user SET user_entity_id = 48 WHERE id = 997;
UPDATE pr_user SET user_entity_id = 48 WHERE id = 1321;
UPDATE pr_user SET user_entity_id = 48 WHERE id = 1364;
UPDATE pr_user SET user_entity_id = 49 WHERE id = 185;
UPDATE pr_user SET user_entity_id = 49 WHERE id = 462;
UPDATE pr_user SET user_entity_id = 49 WHERE id = 1296;
UPDATE pr_user SET user_entity_id = 50 WHERE id = 239;
UPDATE pr_user SET user_entity_id = 50 WHERE id = 516;
UPDATE pr_user SET user_entity_id = 50 WHERE id = 593;
UPDATE pr_user SET user_entity_id = 50 WHERE id = 1234;
UPDATE pr_user SET user_entity_id = 50 WHERE id = 1374;
UPDATE pr_user SET user_entity_id = 50 WHERE id = 1379;
UPDATE pr_user SET user_entity_id = 51 WHERE id = 219;
UPDATE pr_user SET user_entity_id = 51 WHERE id = 290;
UPDATE pr_user SET user_entity_id = 51 WHERE id = 347;
UPDATE pr_user SET user_entity_id = 51 WHERE id = 533;
UPDATE pr_user SET user_entity_id = 51 WHERE id = 912;
UPDATE pr_user SET user_entity_id = 51 WHERE id = 1078;
UPDATE pr_user SET user_entity_id = 51 WHERE id = 1253;
UPDATE pr_user SET user_entity_id = 51 WHERE id = 1319;
UPDATE pr_user SET user_entity_id = 52 WHERE id = 397;
UPDATE pr_user SET user_entity_id = 52 WHERE id = 415;
UPDATE pr_user SET user_entity_id = 52 WHERE id = 862;
UPDATE pr_user SET user_entity_id = 52 WHERE id = 1287;
UPDATE pr_user SET user_entity_id = 52 WHERE id = 1294;
UPDATE pr_user SET user_entity_id = 52 WHERE id = 1345;
UPDATE pr_user SET user_entity_id = 53 WHERE id = 231;
UPDATE pr_user SET user_entity_id = 53 WHERE id = 1314;
UPDATE pr_user SET user_entity_id = 54 WHERE id = 753;
UPDATE pr_user SET user_entity_id = 54 WHERE id = 1230;
UPDATE pr_user SET user_entity_id = 54 WHERE id = 1231;
UPDATE pr_user SET user_entity_id = 54 WHERE id = 1233;
UPDATE pr_user SET user_entity_id = 54 WHERE id = 1236;
UPDATE pr_user SET user_entity_id = 55 WHERE id = 552;
UPDATE pr_user SET user_entity_id = 55 WHERE id = 1033;
UPDATE pr_user SET user_entity_id = 55 WHERE id = 1188;
UPDATE pr_user SET user_entity_id = 56 WHERE id = 770;
UPDATE pr_user SET user_entity_id = 56 WHERE id = 1176;
UPDATE pr_user SET user_entity_id = 56 WHERE id = 1329;
UPDATE pr_user SET user_entity_id = 57 WHERE id = 250;
UPDATE pr_user SET user_entity_id = 57 WHERE id = 376;
UPDATE pr_user SET user_entity_id = 57 WHERE id = 673;
UPDATE pr_user SET user_entity_id = 57 WHERE id = 935;
UPDATE pr_user SET user_entity_id = 57 WHERE id = 937;
UPDATE pr_user SET user_entity_id = 58 WHERE id = 1242;
UPDATE pr_user SET user_entity_id = 58 WHERE id = 1245;
UPDATE pr_user SET user_entity_id = 59 WHERE id = 274;
UPDATE pr_user SET user_entity_id = 59 WHERE id = 282;
UPDATE pr_user SET user_entity_id = 59 WHERE id = 504;
UPDATE pr_user SET user_entity_id = 59 WHERE id = 655;
UPDATE pr_user SET user_entity_id = 59 WHERE id = 1191;
UPDATE pr_user SET user_entity_id = 59 WHERE id = 1237;
UPDATE pr_user SET user_entity_id = 59 WHERE id = 1382;
UPDATE pr_user SET user_entity_id = 60 WHERE id = 762;
UPDATE pr_user SET user_entity_id = 60 WHERE id = 1335;
UPDATE pr_user SET user_entity_id = 60 WHERE id = 1346;
UPDATE pr_user SET user_entity_id = 61 WHERE id = 667;
UPDATE pr_user SET user_entity_id = 61 WHERE id = 1024;
UPDATE pr_user SET user_entity_id = 61 WHERE id = 1300;
UPDATE pr_user SET user_entity_id = 62 WHERE id = 299;
UPDATE pr_user SET user_entity_id = 62 WHERE id = 1146;
UPDATE pr_user SET user_entity_id = 62 WHERE id = 1339;
UPDATE pr_user SET user_entity_id = 63 WHERE id = 300;
UPDATE pr_user SET user_entity_id = 63 WHERE id = 384;
UPDATE pr_user SET user_entity_id = 64 WHERE id = 491;
UPDATE pr_user SET user_entity_id = 64 WHERE id = 537;
UPDATE pr_user SET user_entity_id = 64 WHERE id = 1140;
UPDATE pr_user SET user_entity_id = 64 WHERE id = 1226;
UPDATE pr_user SET user_entity_id = 65 WHERE id = 321;
UPDATE pr_user SET user_entity_id = 66 WHERE id = 1373;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 339;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 340;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 385;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 435;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 484;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 496;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 575;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 576;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 586;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 617;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 656;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 744;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 1124;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 1247;
UPDATE pr_user SET user_entity_id = 67 WHERE id = 1295;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 352;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 366;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 386;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 430;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 507;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 614;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 725;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 837;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 852;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 1152;
UPDATE pr_user SET user_entity_id = 68 WHERE id = 1289;
UPDATE pr_user SET user_entity_id = 69 WHERE id = 1351;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 392;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 543;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 564;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 637;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 703;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 871;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 961;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 1110;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 1153;
UPDATE pr_user SET user_entity_id = 70 WHERE id = 1291;
UPDATE pr_user SET user_entity_id = 71 WHERE id = 395;
UPDATE pr_user SET user_entity_id = 72 WHERE id = 1135;
UPDATE pr_user SET user_entity_id = 72 WHERE id = 1160;
UPDATE pr_user SET user_entity_id = 73 WHERE id = 424;
UPDATE pr_user SET user_entity_id = 74 WHERE id = 429;
UPDATE pr_user SET user_entity_id = 74 WHERE id = 861;
UPDATE pr_user SET user_entity_id = 74 WHERE id = 1174;
UPDATE pr_user SET user_entity_id = 75 WHERE id = 981;
UPDATE pr_user SET user_entity_id = 75 WHERE id = 1293;
UPDATE pr_user SET user_entity_id = 76 WHERE id = 483;
UPDATE pr_user SET user_entity_id = 76 WHERE id = 522;
UPDATE pr_user SET user_entity_id = 76 WHERE id = 714;
UPDATE pr_user SET user_entity_id = 76 WHERE id = 763;
UPDATE pr_user SET user_entity_id = 76 WHERE id = 793;
UPDATE pr_user SET user_entity_id = 76 WHERE id = 800;
UPDATE pr_user SET user_entity_id = 77 WHERE id = 493;
UPDATE pr_user SET user_entity_id = 77 WHERE id = 495;
UPDATE pr_user SET user_entity_id = 79 WHERE id = 1207;
UPDATE pr_user SET user_entity_id = 80 WHERE id = 558;
UPDATE pr_user SET user_entity_id = 81 WHERE id = 570;
UPDATE pr_user SET user_entity_id = 82 WHERE id = 696;
UPDATE pr_user SET user_entity_id = 82 WHERE id = 1050;
UPDATE pr_user SET user_entity_id = 82 WHERE id = 1324;
UPDATE pr_user SET user_entity_id = 83 WHERE id = 697;
UPDATE pr_user SET user_entity_id = 83 WHERE id = 855;
UPDATE pr_user SET user_entity_id = 83 WHERE id = 931;
UPDATE pr_user SET user_entity_id = 84 WHERE id = 705;
UPDATE pr_user SET user_entity_id = 84 WHERE id = 878;
UPDATE pr_user SET user_entity_id = 85 WHERE id = 909;
UPDATE pr_user SET user_entity_id = 85 WHERE id = 1156;
UPDATE pr_user SET user_entity_id = 86 WHERE id = 754;
UPDATE pr_user SET user_entity_id = 87 WHERE id = 1201;
UPDATE pr_user SET user_entity_id = 88 WHERE id = 1085;
UPDATE pr_user SET user_entity_id = 89 WHERE id = 1182;
UPDATE pr_user SET user_entity_id = 90 WHERE id = 872;
UPDATE pr_user SET user_entity_id = 91 WHERE id = 896;
UPDATE pr_user SET user_entity_id = 91 WHERE id = 899;



ALTER TABLE innovation ADD sort_score INT DEFAULT NULL;


ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AA76ED395;
ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA76ED395 FOREIGN KEY (user_id) REFERENCES pr_user (id) ON DELETE SET NULL;
ALTER TABLE metrics DROP FOREIGN KEY FK_228AAAE7A76ED395;
ALTER TABLE metrics DROP FOREIGN KEY FK_228AAAE7948007BF;
ALTER TABLE metrics ADD CONSTRAINT FK_228AAAE7A76ED395 FOREIGN KEY (user_id) REFERENCES pr_user (id) ON DELETE SET NULL;
ALTER TABLE metrics ADD CONSTRAINT FK_228AAAE7948007BF FOREIGN KEY (innovation_id) REFERENCES innovation (id) ON DELETE SET NULL;


INSERT INTO `business_driver` VALUES (6,'Specialty','specialty');


-- ADDING USER SKILLS FONCTIONNALITY

CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, is_main_skill TINYINT(1) NOT NULL, INDEX tag_title_idx (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;

CREATE TABLE user_skill (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, skill_id INT DEFAULT NULL, sender_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_BCFF1F2FA76ED395 (user_id), INDEX IDX_BCFF1F2F5585C142 (skill_id), INDEX IDX_BCFF1F2FF624B39D (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;
ALTER TABLE user_skill ADD CONSTRAINT FK_BCFF1F2FA76ED395 FOREIGN KEY (user_id) REFERENCES pr_user (id);
ALTER TABLE user_skill ADD CONSTRAINT FK_BCFF1F2F5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id);
ALTER TABLE user_skill ADD CONSTRAINT FK_BCFF1F2FF624B39D FOREIGN KEY (sender_id) REFERENCES pr_user (id);


INSERT INTO `skill` (title, is_main_skill) VALUES
("Design thinking", 1),
("New Business Models", 1),
("Real-life Experimentation", 1),
("Business case", 1),
("Entrepreneurial spirit", 1),
("Test & Learn", 1),
("Finance", 1),
("Marketing", 1),
("Commercial", 1),
("R&D", 1),
("Product activation", 1),
("e-commerce", 1),
("On-trade", 1),
("Off-trade", 1),
("RTD / RTS", 1),
("Can", 1),
("Organic", 1),
("Micro-production", 1),
("Low abv", 1),
("Zero alcohol", 1),
("Flavors", 1),
("Line extensions", 1),
("Liquid color", 1),
("Rum", 1),
("Gin", 1),
("Champagne / Sparkling wine", 1),
("Wine", 1),
("Cognac", 1),
("Whisky", 1),
("Vodka", 1),
("Liqueur", 1),
("Brandy", 1),
("Tequila / Mezcal", 1),
("Craft", 1),
("Fair trade", 1);



-- VERSION 6.2 LOT 2

ALTER TABLE innovation ADD universal_key_information_3_vs VARCHAR(255) DEFAULT NULL,
ADD universal_key_information_4_vs VARCHAR(255) DEFAULT NULL;
ALTER TABLE innovation ADD alcohol_by_volume VARCHAR(255) DEFAULT NULL;


-- new moment_of_consumptions
UPDATE innovation set moment_of_consumption_id = null;
DELETE FROM moment_of_consumption WHERE id in (2,3,4,5);
INSERT INTO `moment_of_consumption` (title, css_class) VALUES
("Kicking back", "kicking_back"),
("My time", "my_time"),
("Daily Calm", "daily_calm"),
("Special dinner and drinks", "special_dinner_and_drinks"),
("Sip & Savour", "sip_savour"),
("Chilling with friends", "chilling_with_friends"),
("Living the good life", "living_the_good_life"),
("Premium socialising", "premium_socialising"),
("Enjoying the good times", "enjoying_the_good_times"),
("Be seen while socialising", "be_see_while_socialising"),
("The night is ours", "the_night_is_ours"),
("Trendu night out", "trendu_night_out");



-- update service/experience to service
UPDATE classification set title = "Service", css_class = "service" WHERE id = 3;
