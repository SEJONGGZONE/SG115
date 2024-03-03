-- MySQL dump 10.16  Distrib 10.3.10-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: gzonesoft_db
-- ------------------------------------------------------
-- Server version	10.3.10-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `insdebug_tb`
--

DROP TABLE IF EXISTS `insdebug_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `insdebug_tb` (
  `d_uid` int(11) NOT NULL AUTO_INCREMENT,
  `d_key` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '검색 키',
  `d_content` text CHARACTER SET utf8 DEFAULT NULL COMMENT '내용 ',
  `d_tracking` text CHARACTER SET utf8 DEFAULT NULL COMMENT '실행추적(debug_backtrace() 참고)  ',
  `d_ip` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '아이피',
  `d_file` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '실행된 파일 경로',
  `d_rdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`d_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='디버깅 :: lib.func.php 에 insdebug 함수 필요';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `insdebug_tb`
--

LOCK TABLES `insdebug_tb` WRITE;
/*!40000 ALTER TABLE `insdebug_tb` DISABLE KEYS */;
/*!40000 ALTER TABLE `insdebug_tb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_2year_opt_log`
--

DROP TABLE IF EXISTS `smart_2year_opt_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_2year_opt_log` (
  `ol_uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ol_mid` varchar(50) NOT NULL DEFAULT '' COMMENT '저장회원ID',
  `ol_type` enum('email','sms','both') NOT NULL DEFAULT 'email' COMMENT '발송형태',
  `ol_status` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '발송상태',
  `ol_rdate` datetime DEFAULT NULL COMMENT '저장일',
  `ol_sdate` datetime DEFAULT NULL COMMENT '발송일',
  PRIMARY KEY (`ol_uid`),
  KEY `ol_mid` (`ol_mid`),
  KEY `ol_type` (`ol_type`),
  KEY `ol_status` (`ol_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='매2년 수신동의 재확인 - 메일/문자 발송 기록';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_2year_opt_log`
--

LOCK TABLES `smart_2year_opt_log` WRITE;
/*!40000 ALTER TABLE `smart_2year_opt_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_2year_opt_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_admin`
--

DROP TABLE IF EXISTS `smart_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_admin` (
  `a_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `a_id` varchar(50) NOT NULL COMMENT '계정아이디',
  `a_pw` varchar(255) NOT NULL COMMENT '비밀번호',
  `a_type` enum('admin','master') NOT NULL DEFAULT 'admin' COMMENT '회원유형(master : 삭제불가)',
  `a_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '관리자활성여여부',
  `a_rpw` varchar(255) NOT NULL COMMENT '비밀번호 확인',
  `a_name` varchar(50) NOT NULL COMMENT '이름/닉네임',
  `a_htel` varchar(20) NOT NULL COMMENT '휴대폰',
  `a_email` varchar(255) NOT NULL COMMENT '이메일',
  `a_job_title` varchar(50) DEFAULT NULL COMMENT '직급명',
  `a_corrosion_name` varchar(50) DEFAULT NULL COMMENT '부서명',
  `a_reg_aid` varchar(50) DEFAULT NULL COMMENT '등록한 관리자',
  `a_mdate` datetime NOT NULL COMMENT '수정일',
  `a_rdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`a_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 운영자 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_admin`
--

LOCK TABLES `smart_admin` WRITE;
/*!40000 ALTER TABLE `smart_admin` DISABLE KEYS */;
INSERT INTO `smart_admin` VALUES (1,'master','*4999DBFBDAA3384212A7934E8106780D2386C983','master','Y','','최고관리자','01000000000','tech@onedaynet.co.kr','관리자','최고',NULL,'2023-01-20 11:21:18','2023-01-20 00:00:00'),(22,'admin','*AA803D048B666A933E512AA53B36C70174A37D1E','admin','Y','','부관리자','000-0000-0000','help@domain.com','','',NULL,'2023-01-20 14:52:14','2023-01-20 21:26:36');
/*!40000 ALTER TABLE `smart_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_admin_menu`
--

DROP TABLE IF EXISTS `smart_admin_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_admin_menu` (
  `am_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `am_idx` int(11) NOT NULL COMMENT '순서',
  `am_depth` int(11) NOT NULL DEFAULT 1 COMMENT '차수',
  `am_view` enum('Y','N') NOT NULL COMMENT '노출여부',
  `am_parent` varchar(200) NOT NULL DEFAULT '0' COMMENT '부모uid',
  `am_name` varchar(200) NOT NULL COMMENT '매뉴이름',
  `am_link` varchar(255) NOT NULL DEFAULT '' COMMENT '링크',
  PRIMARY KEY (`am_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=330 DEFAULT CHARSET=utf8 COMMENT='관리자메뉴관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_admin_menu`
--

LOCK TABLES `smart_admin_menu` WRITE;
/*!40000 ALTER TABLE `smart_admin_menu` DISABLE KEYS */;
INSERT INTO `smart_admin_menu` VALUES (1,10,1,'Y','','환경설정',''),(2,1,2,'Y','1','기본설정',''),(3,1,3,'Y','1,2','쇼핑몰 기본정보','_config.default.form.php'),(4,2,3,'Y','1,2','약관 및 정책 설정','_config.agree.form.php'),(6,4,3,'Y','1,2','이용안내 설정','_config.usage.php'),(7,2,2,'Y','1','운영 관리 설정',''),(8,0,3,'Y','1,7','관리자  메뉴관리','_config.admin_menu.list.php'),(9,2,3,'Y','1,7','운영자 목록','_config.admin.list.php'),(10,3,3,'Y','1,7','운영자별 메뉴 노출설정','_config.admin_menuset.form.php'),(285,1,3,'Y','267,282','상품 리뷰 목록','_product_talk.list.php?pt_type=상품리뷰'),(286,2,3,'Y','267,282','상품 문의 목록','_product_talk.list.php?pt_type=상품문의'),(13,7,3,'Y','1,7','솔루션 이용현황','_solution.info.php'),(284,2,3,'Y','267,281','제휴 문의 목록','_request.list.php?pass_menu=partner'),(15,9,3,'Y','1,7','SNS 로그인/API 설정','_config.sns.form.php'),(16,12,3,'Y','1,7','파비콘/공유이미지 설정','_config.device.form.php'),(17,4,2,'Y','1','결제 관련 설정',''),(18,1,3,'Y','1,17','결제 수단 사용 설정','_config.paymethod.php'),(19,9,3,'Y','1,17','무통장입금 은행 관리','_config.none_bank.php'),(20,2,3,'Y','1,17','통합 전자결제(PG) 관리','_config.pg.form.php'),(300,1,3,'Y','1,273','회원 /비회원 기본 설정','_config.member.orderway.form.php'),(256,13,3,'Y','1,7','에디터 등록 이미지 관리','_config.editor_img.list.php'),(31,6,2,'Y','1','상품/배송 /정산 설정',''),(32,4,3,'Y','1,31','배송 기본설정','_config.delivery.form.php'),(33,5,3,'Y','1,31','도서산간 추가배송비 설정','_config.delivery_addprice.list.php'),(283,1,3,'Y','267,281','1:1  온라인 문의 목록','_request.list.php?pass_menu=inquiry'),(276,8,3,'Y','1,273','비밀번호 관련설정','_config.password.config.php'),(272,8,3,'Y','1,2','보안서버 설정','_config.ssl.default_form.php'),(273,7,2,'Y','1','회원 관련 설정',''),(44,11,1,'Y','','회원관리',''),(45,1,2,'Y','44','회원 관리',''),(46,1,3,'Y','44,45','회원 목록','_individual.list.php'),(324,5,3,'Y','75,88','타임세일 상품설정','_config.display.timesale.php'),(319,2,3,'Y','44,318','회원등급 정책','_config.group.php'),(322,3,3,'Y','44,318','가입 승인 및 입력항목','_config.join.php'),(318,2,2,'Y','44','회원등급 및 정책',''),(51,7,3,'Y','44,45','휴면  회원 목록','_individual_sleep.list.php'),(52,8,3,'Y','44,45','탈퇴 회원 목록','_individual_out.list.php'),(54,5,2,'Y','44','적립금 관리',''),(55,2,3,'Y','44,54','자동 적립금 설정','_config.point.form.php'),(56,1,3,'Y','44,54','적립금 내역','_point.list.php'),(64,3,2,'Y','1','SMS/알림톡 관리',''),(65,1,3,'Y','1,64','SMS/알림톡 정보설정','_config.sms.form.php'),(66,2,3,'Y','1,64','개별/전체 SMS 발송','_sms.form.php'),(67,3,3,'Y','1,64','발송내역','_config.sms.out_send_list.php'),(68,4,3,'Y','1,64','충전관리','_config.sms.out_list.php'),(70,6,2,'Y','44','메일링 관리',''),(311,11,3,'Y','75,88','상품 아이콘 관리','_product_icon.list.php'),(73,3,3,'Y','44,70','이메일 발송 목록','_mailing_data.list.php'),(253,12,3,'Y','1,17','주문 취소 관련설정','_config.paycancel.php'),(75,14,1,'Y','','상품관리',''),(76,1,2,'Y','75','상품 관리',''),(77,1,3,'Y','75,76','배송상품 목록','_product.list.php'),(78,2,3,'N','75,76','배송 상품 등록','_product.form.php'),(312,3,3,'Y','267,282','메인 리뷰 설정','_config.display.review.php'),(80,6,3,'Y','75,76','자주쓰는 옵션 관리','_product.common_option_set.list.php'),(81,7,3,'Y','75,76','찜한상품 목록','_product_wish.list.php'),(82,1,2,'Y','75','상품 일괄 관리',''),(83,1,3,'Y','75,82','가격 /정산 설정','_product_mass.price.php'),(84,2,3,'Y','75,82','노출/재고 설정','_product_mass.view.php'),(85,3,3,'Y','75,82','적립율/상품쿠폰 설정','_product_mass.point.php'),(86,4,3,'Y','75,82','카테고리 이동/복사/추가','_product_mass.move.php'),(87,5,3,'Y','75,82','옵션 설정','_product_mass.option.php'),(88,2,2,'Y','75','상품 진열 관리',''),(89,1,3,'Y','75,88','메인 노출 상품','_config.display.main.php'),(90,6,3,'Y','75,88','상품 검색 설정','_config.display.search.php'),(92,5,2,'Y','75','카테고리 관리',''),(93,1,3,'Y','75,92','상품 카테고리','_category.list.php'),(94,2,3,'Y','75,92','브랜드 설정','_brand.list.php'),(95,15,1,'Y','','주문/배송',''),(96,1,2,'Y','95','주문관리',''),(97,3,3,'Y','95,96','네이버페이 주문','_npay_order.list.php'),(98,1,3,'Y','95,96','전체 주문 ','_order.list.php'),(99,2,3,'Y','95,96','입금대기 주문','_order.list.php?view=online'),(245,4,3,'Y','95,102','상품별 배송처리','_order_product.list.php'),(102,2,2,'Y','95','배송/발급 관리',''),(103,1,3,'Y','95,102','주문별 배송처리','_order_delivery.list.php'),(109,3,2,'Y','95','취소/교환/반품/환불',''),(111,3,3,'Y','95,109','부분 취소 목록','_cancel.list.php'),(112,4,3,'Y','95,109','교환/반품 신청 목록','_order_complain.list.php'),(113,5,3,'Y','95,109','환불 요청 목록','_cancel_order.list.php'),(117,6,2,'Y','95','자동 입금 확인',''),(120,3,3,'Y','95,117','실시간 입금 확인 내역','_orderbanklog.list.php'),(121,4,3,'Y','95,117','미확인 입금자 관리','_online_notice.list.php'),(122,7,2,'Y','95','현금영수증 관리',''),(123,1,3,'Y','95,122','현금영수증 발급 내역','_cashbill.list.php'),(124,2,3,'Y','95,122','현금영수증 개별발급','_cashbill.form.php'),(126,18,1,'Y','','게시판',''),(127,1,2,'Y','126','게시판 관리',''),(128,1,3,'Y','126,127','게시판 목록','_bbs.board.list.php'),(129,6,3,'N','126,127','게시판 등록','_bbs.board.form.php'),(130,2,3,'Y','126,127','게시글 전체 목록','_bbs.post_mng.list.php'),(132,3,3,'Y','126,127','게시판 금지어 관리','_bbs.forbidden_word.form.php'),(133,5,3,'Y','126,127','게시글 양식 관리','_bbs.post_template.list.php'),(270,2,3,'Y','126,268','분류 및 키워드 관리','_bbs.post_faq_type.form.php'),(135,19,1,'Y','','디자인',''),(136,1,2,'Y','135','배너/팝업 관리',''),(137,2,3,'Y','135,136','팝업 관리','_popup.list.php'),(138,1,3,'Y','135,136','배너 관리','_banner.list.php'),(252,7,3,'Y','1,2','페이지 타이틀 설정','_config.title.form.php'),(140,20,1,'Y','','프로모션',''),(141,1,2,'Y','140','쿠폰 관리',''),(142,2,3,'Y','140,141','쿠폰 주문관련 설정','_coupon_config.php'),(143,1,3,'Y','140,141','쿠폰 목록','_coupon_set.list.php'),(327,1,3,'Y','75,326','노출 항목설정','_config.display.pinfo.php'),(145,2,2,'Y','140','프로모션코드 관리',''),(146,1,3,'Y','140,145','프로모션코드 목록','_promotion.list.php'),(147,3,2,'Y','140','기획전 관리',''),(264,7,3,'Y','95,102','티켓  발급목록','_order_ticket.list.php'),(149,4,3,'N','140,147','출석체크 등록','_promotion_attend.form.php'),(151,1,3,'Y','140,147','기획전 목록','_promotion_plan.list.php'),(255,3,3,'Y','1,17','간편결제 설정','_config.pg_easypay.form.php'),(309,1,3,'Y','140,308','출석체크 목록','_promotion_attend.list.php'),(308,4,2,'Y','140','출석체크 관리',''),(307,3,3,'Y','95,304','정산 현황','_ordercalc.view.php'),(305,1,3,'Y','95,304','정산 대기 목록','_order3.list.php'),(306,2,3,'Y','95,304','정산 완료 목록','_order4.list.php'),(304,5,2,'Y','95','정산관리',''),(328,2,3,'Y','75,326','이용안내  관리','_product.guide.list.php'),(323,6,3,'Y','1,17','부가세율 설정','_config.vat.form.php'),(182,21,1,'Y','','로그분석',''),(183,1,2,'Y','182','방문자 분석',''),(184,1,3,'Y','182,183','시간 및 날짜별','_cntlog.php'),(185,2,3,'Y','182,183','접속경로  및 키워드별','_cntlog_route.php'),(186,3,3,'Y','182,183','접속환경 및 회원분석','_cntlog_env.php'),(187,4,3,'Y','182,183','상세 유입경로','_cntlog_detail.php'),(188,2,2,'Y','182','회원분석',''),(189,1,3,'Y','182,188','회원 가입 형태 ','_static_mem.method.php'),(190,2,3,'Y','182,188','회원 상태 ','_static_mem.type.php'),(191,3,3,'Y','182,188','회원 적립금','_static_mem.point.php'),(192,3,2,'Y','182','상품분석',''),(193,3,3,'Y','182,192','카테고리 판매 순위','_static_product.category.php'),(194,2,3,'Y','182,192','구매통계 및 상품  판매순위','_static_product.order.php'),(196,6,3,'Y','182,192','장바구니 담긴 상품 순위 ','_static_product.cart.php'),(197,7,3,'Y','182,192','찜한상품 순위','_static_product.wish.php'),(198,4,2,'Y','182','매출분석',''),(199,1,3,'Y','182,198','전체 매출 통계','_static_sale.all.php'),(200,2,3,'Y','182,198','연령별 매출','_static_sale.age.php'),(201,1,3,'Y','182,198','결제수단별 매출','_static_sale.method.php'),(202,4,3,'Y','182,198','배송지별 매출','_static_sale.area.php'),(203,5,2,'Y','182','주문분석',''),(204,1,3,'Y','182,203','전체 주문 통계','_static_order.all.php'),(205,1,3,'Y','182,203','연령별 주문','_static_order.age.php'),(206,4,3,'Y','182,203','지역별 주문','_static_order.area.php'),(207,2,3,'Y','182,203','성별 주문','_static_order.sex.php'),(321,1,3,'Y','44,318','회원등급 관리','_member_group_set.list.php'),(320,4,3,'Y','44,318','휴면회원 정책','_config.sleep.php'),(216,8,2,'Y','95','전자세금계산서 관리',''),(219,3,3,'Y','95,216','세금계산서 발급 내역','_tax.list.php'),(220,2,2,'Y','135',' 디자인 관리',''),(221,3,3,'Y','135,220','스킨 관리','_skin.php'),(223,1,3,'Y','135,220','일반페이지 관리','_normalpage.list.php'),(225,5,3,'Y','1,17','네이버페이 설정','_config.pg_naver.form.php'),(227,5,3,'Y','1,2','문자/이메일 수신관련설정','_addons.php?_menuType=smsEmail'),(228,13,3,'Y','1,7','자주쓰는 메뉴 설정','_config.favmenu.list.php'),(231,11,3,'Y','1,17','실시간 입금확인 서비스','_config.orderbank.form.php'),(232,8,3,'Y','1,17','바로빌 서비스','_config.tax.form.php'),(313,5,3,'Y','1,64','SMS 에러로그','_sms.log.php'),(249,1,3,'Y','95,109','주문 취소 목록','_order.cancel_list.php'),(240,7,3,'Y','75,88','타입별 상품관리','_config.display.type.php'),(243,6,3,'Y','1,2','080 수신거부 관련설정','_addons.php?_menuType=080'),(250,4,3,'Y','95,216','세금계산서 개별발급','_tax.form.php'),(267,17,1,'Y','','리뷰/문의',''),(262,3,3,'Y','75,76','티켓상품 목록','_product_ticket.list.php'),(263,4,3,'N','75,76','티켓 상품 등록','_product_ticket.form.php'),(326,4,2,'Y','75','상품 상세페이지 설정',''),(265,2,3,'Y','75,88','메인 카테고리 베스트 상품','_config.display.category.php'),(303,1,3,'Y','1,31','상품/정산 기본설정','_config.product.form.php'),(268,2,2,'Y','126','FAQ 관리',''),(269,1,3,'Y','126,268','FAQ  전체 목록','_bbs.post_faq.list.php'),(274,9,3,'Y','1,273','본인확인 서비스 ','_config.member.form.php'),(282,1,2,'Y','267',' 상품 리뷰/문의 관리',''),(279,10,3,'Y','1,273','접속 로그 설정','_config.cntlog.form.php'),(281,1,2,'Y','267','상담 관리','');
/*!40000 ALTER TABLE `smart_admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_admin_menu_set`
--

DROP TABLE IF EXISTS `smart_admin_menu_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_admin_menu_set` (
  `ams_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `ams_amuid` int(11) DEFAULT NULL COMMENT '메뉴고유번호',
  `ams_auid` int(11) DEFAULT NULL COMMENT '운영자고유번호',
  PRIMARY KEY (`ams_uid`),
  UNIQUE KEY `ams_unique` (`ams_amuid`,`ams_auid`),
  KEY `ams_amuid` (`ams_amuid`),
  KEY `ams_auid` (`ams_auid`)
) ENGINE=MyISAM AUTO_INCREMENT=2060 DEFAULT CHARSET=utf8 COMMENT='관리자별 메뉴관리 시 메뉴 허용일때 생성';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_admin_menu_set`
--

LOCK TABLES `smart_admin_menu_set` WRITE;
/*!40000 ALTER TABLE `smart_admin_menu_set` DISABLE KEYS */;
INSERT INTO `smart_admin_menu_set` VALUES (1674,233,18),(544,195,14),(474,125,14),(465,116,14),(464,115,14),(463,114,14),(297,0,14),(725,58,14),(724,57,14),(1871,2,22),(1870,1,22),(1869,3,22),(1863,2,14),(1862,1,14),(1861,3,14),(1673,232,18),(1672,231,18),(1671,228,18),(1670,227,18),(1669,225,18),(1663,219,18),(1662,218,18),(1661,217,18),(1660,216,18),(1659,209,18),(1658,208,18),(1657,207,18),(1656,206,18),(1655,205,18),(1654,204,18),(1653,203,18),(1652,202,18),(1651,201,18),(1650,200,18),(1649,199,18),(1648,198,18),(1647,197,18),(1646,196,18),(1645,194,18),(1644,193,18),(1643,192,18),(1642,191,18),(1641,190,18),(1640,189,18),(1639,188,18),(1638,187,18),(1637,186,18),(1636,185,18),(1635,184,18),(1634,183,18),(1633,182,18),(1632,181,18),(1631,180,18),(1630,179,18),(1629,178,18),(1628,177,18),(1627,176,18),(1626,175,18),(1625,174,18),(1624,173,18),(1623,172,18),(1622,171,18),(1621,170,18),(1620,169,18),(1619,168,18),(1618,167,18),(1617,166,18),(1616,165,18),(1615,164,18),(1614,163,18),(1613,162,18),(1612,161,18),(1611,160,18),(1610,159,18),(1609,158,18),(1608,157,18),(1607,156,18),(1606,155,18),(1605,154,18),(1604,153,18),(1603,152,18),(1731,140,18),(1803,220,18),(1802,221,18),(1801,135,18),(1585,134,18),(1584,133,18),(1583,132,18),(1582,131,18),(1581,130,18),(1580,129,18),(1579,128,18),(1578,127,18),(1577,126,18),(1576,124,18),(1575,123,18),(1574,122,18),(1573,121,18),(1572,120,18),(1571,119,18),(1570,118,18),(1569,117,18),(1568,116,18),(1567,115,18),(1566,114,18),(1565,113,18),(1564,112,18),(1563,111,18),(1562,110,18),(1561,109,18),(1560,108,18),(1559,107,18),(1558,106,18),(1557,105,18),(1556,104,18),(1555,103,18),(1554,102,18),(1553,101,18),(1552,100,18),(1551,99,18),(1550,98,18),(1549,97,18),(1548,96,18),(1547,95,18),(1546,94,18),(1545,93,18),(1544,92,18),(1543,91,18),(1542,90,18),(1541,89,18),(1540,88,18),(1539,87,18),(1538,86,18),(1537,85,18),(1536,84,18),(1535,83,18),(1534,82,18),(1533,81,18),(1532,80,18),(1531,79,18),(1530,78,18),(1529,77,18),(1528,76,18),(1527,75,18),(1526,74,18),(1525,73,18),(1524,72,18),(1523,71,18),(1522,70,18),(1521,69,18),(1520,68,18),(1519,67,18),(1518,66,18),(1517,65,18),(1516,64,18),(1515,63,18),(1514,62,18),(1513,61,18),(1512,60,18),(1511,59,18),(1510,56,18),(1509,55,18),(1508,54,18),(1507,53,18),(1506,52,18),(1505,51,18),(1504,50,18),(1503,49,18),(1502,48,18),(1501,47,18),(1500,46,18),(1499,45,18),(1498,44,18),(1497,43,18),(1496,42,18),(1495,41,18),(1494,40,18),(1493,39,18),(1492,38,18),(1491,37,18),(1490,36,18),(1489,35,18),(1488,34,18),(1487,33,18),(1486,32,18),(1485,31,18),(1484,22,18),(1483,21,18),(1482,20,18),(1481,19,18),(1480,18,18),(1479,17,18),(1478,16,18),(1477,15,18),(1476,14,18),(1475,13,18),(1474,12,18),(1473,11,18),(1472,10,18),(1471,9,18),(1470,8,18),(1469,7,18),(1468,6,18),(1467,5,18),(1466,4,18),(1465,3,18),(1464,2,18),(1463,1,18),(1462,80,19),(1461,79,19),(1460,78,19),(1459,77,19),(1458,76,19),(1457,75,19),(1675,240,18),(1676,126,19),(1677,127,19),(1678,128,19),(1679,129,19),(1680,130,19),(1681,1,19),(1730,140,19),(1872,1,23),(1873,2,23),(1874,3,23),(1875,4,23),(1876,5,23),(1877,6,23),(1878,7,23),(2059,8,23),(1880,9,23),(1881,10,23),(1882,285,23),(1883,286,23),(1884,13,23),(1885,284,23),(1886,15,23),(1887,16,23),(1888,17,23),(1889,18,23),(1890,19,23),(1891,20,23),(1892,21,23),(1893,256,23),(1894,31,23),(1895,32,23),(1896,33,23),(1897,34,23),(1898,283,23),(1899,276,23),(1900,272,23),(1901,273,23),(1902,258,23),(1903,260,23),(1904,254,23),(1905,44,23),(1906,45,23),(1907,46,23),(1908,47,23),(1909,48,23),(1910,49,23),(1911,50,23),(1912,51,23),(1913,52,23),(1914,54,23),(1915,55,23),(1916,56,23),(1917,291,23),(1918,289,23),(1919,290,23),(1920,288,23),(1921,287,23),(1922,64,23),(1923,65,23),(1924,66,23),(1925,67,23),(1926,68,23),(1927,70,23),(1928,73,23),(1929,253,23),(1930,75,23),(1936,82,23),(1937,83,23),(1938,84,23),(1939,85,23),(1940,86,23),(1941,87,23),(1942,88,23),(1943,89,23),(1944,90,23),(1945,91,23),(1946,92,23),(1947,93,23),(1948,94,23),(1949,95,23),(1950,96,23),(1951,97,23),(1952,98,23),(1953,99,23),(1954,245,23),(1955,102,23),(1956,103,23),(1957,106,23),(1958,107,23),(1959,108,23),(1960,109,23),(1961,111,23),(1962,112,23),(1963,113,23),(1964,117,23),(1965,120,23),(1966,121,23),(1967,122,23),(1968,123,23),(1969,124,23),(1970,126,23),(1971,127,23),(1972,128,23),(1973,129,23),(1974,130,23),(1975,132,23),(1976,133,23),(1977,270,23),(1978,135,23),(1979,136,23),(1980,137,23),(1981,138,23),(1982,252,23),(1983,140,23),(1984,141,23),(1985,142,23),(1986,143,23),(1987,144,23),(1988,145,23),(1989,146,23),(1990,147,23),(1991,264,23),(1992,149,23),(1993,150,23),(1994,151,23),(1995,255,23),(1996,248,23),(1997,182,23),(1998,183,23),(1999,184,23),(2000,185,23),(2001,186,23),(2002,187,23),(2003,188,23),(2004,189,23),(2005,190,23),(2006,191,23),(2007,192,23),(2008,193,23),(2009,194,23),(2010,196,23),(2011,197,23),(2012,198,23),(2013,199,23),(2014,200,23),(2015,201,23),(2016,202,23),(2017,203,23),(2018,204,23),(2019,205,23),(2020,206,23),(2021,207,23),(2022,208,23),(2023,209,23),(2024,216,23),(2025,219,23),(2026,220,23),(2027,221,23),(2028,223,23),(2029,225,23),(2030,227,23),(2031,228,23),(2032,231,23),(2033,232,23),(2034,233,23),(2035,249,23),(2036,240,23),(2037,243,23),(2038,250,23),(2039,251,23),(2040,259,23),(2041,267,23),(2043,265,23),(2044,271,23),(2045,268,23),(2046,269,23),(2047,274,23),(2048,282,23),(2049,278,23),(2050,279,23),(2051,281,23),(2052,292,23),(2053,293,23),(2054,294,23),(2055,295,23),(2056,296,23),(2057,297,23),(2058,298,23);
/*!40000 ALTER TABLE `smart_admin_menu_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_apply_auth_count`
--

DROP TABLE IF EXISTS `smart_apply_auth_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_apply_auth_count` (
  `aac_uid` int(11) NOT NULL AUTO_INCREMENT,
  `aac_type` varchar(50) DEFAULT NULL COMMENT '타입 고유값정의 (필수)',
  `aac_key` varchar(50) DEFAULT NULL COMMENT '키값정의(필수)',
  `aac_count` int(11) DEFAULT NULL COMMENT '카운팅(duplicate key update  에 의해 증가)',
  `aac_udate` datetime DEFAULT NULL COMMENT '업데이트일-최종 업데이트 시간기준',
  `aac_rdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`aac_uid`) USING BTREE,
  UNIQUE KEY `aac_type_ac_key` (`aac_type`,`aac_key`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='특정 프로그램 시도 횟수 카운팅 DB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_apply_auth_count`
--

LOCK TABLES `smart_apply_auth_count` WRITE;
/*!40000 ALTER TABLE `smart_apply_auth_count` DISABLE KEYS */;
INSERT INTO `smart_apply_auth_count` VALUES (1,'totalAdminLogin','master',0,'2024-01-22 09:53:11','2024-02-27 10:21:05');
/*!40000 ALTER TABLE `smart_apply_auth_count` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_bank_set`
--

DROP TABLE IF EXISTS `smart_bank_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_bank_set` (
  `bs_uid` int(11) NOT NULL AUTO_INCREMENT,
  `bs_bank_name` varchar(100) NOT NULL COMMENT '은행이름',
  `bs_bank_num` varchar(100) NOT NULL COMMENT '계좌번호',
  `bs_user_name` varchar(100) NOT NULL COMMENT '예금주',
  `bs_idx` int(11) NOT NULL DEFAULT 99 COMMENT '우선순위',
  PRIMARY KEY (`bs_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='무통장 계좌 정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_bank_set`
--

LOCK TABLES `smart_bank_set` WRITE;
/*!40000 ALTER TABLE `smart_bank_set` DISABLE KEYS */;
INSERT INTO `smart_bank_set` VALUES (1,'국민','000000-00-111111','홍길동',1),(2,'농협','000000-00-222222','홍길동',2);
/*!40000 ALTER TABLE `smart_bank_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_banner`
--

DROP TABLE IF EXISTS `smart_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_banner` (
  `b_uid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `b_loc` varchar(100) NOT NULL COMMENT '위치값',
  `b_img` varchar(30) DEFAULT NULL COMMENT '배너이미지',
  `b_img_mo` varchar(30) DEFAULT NULL COMMENT '배너이미지 모바일',
  `b_link` varchar(200) DEFAULT NULL COMMENT '배너링크',
  `b_link_btnname` varchar(50) DEFAULT NULL COMMENT '링크 이동 버튼명',
  `b_target` enum('_self','_blank','_none') NOT NULL DEFAULT '_self' COMMENT '링크방식(_self: 현재창, _self: 새창, _none: 링크없음)',
  `b_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '노출여부',
  `b_title` varchar(200) DEFAULT NULL COMMENT '배너타이틀',
  `b_info` varchar(255) DEFAULT NULL COMMENT '배너설명1',
  `b_sub_info` varchar(255) DEFAULT NULL COMMENT '배너설명2',
  `b_text` text DEFAULT NULL COMMENT '배너 설명',
  `b_idx` int(5) NOT NULL DEFAULT 0 COMMENT '사이트 순서',
  `b_none_limit` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '무기한 배너여부(Y:무기한, 기한)',
  `b_color` varchar(7) NOT NULL COMMENT '배경색',
  `b_site_skin` varchar(100) NOT NULL COMMENT '스킨',
  `b_sdate` date NOT NULL COMMENT '시작일 - 00 시부터 시작',
  `b_edate` date NOT NULL COMMENT '종료일 - 23시 59분 59초까지',
  `b_rdate` datetime DEFAULT NULL COMMENT '배너등록일',
  `b_white_img` varchar(30) DEFAULT NULL COMMENT '로고 흰색이미지',
  PRIMARY KEY (`b_uid`),
  KEY `b_loc` (`b_loc`),
  KEY `b_target` (`b_target`),
  KEY `b_view` (`b_view`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COMMENT='배너관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_banner`
--

LOCK TABLES `smart_banner` WRITE;
/*!40000 ALTER TABLE `smart_banner` DISABLE KEYS */;
INSERT INTO `smart_banner` VALUES (37,'responsive-food,mobile_top_logo','3951290073.svg','','/','','_self','Y','Title','','','',0,'Y','','responsive-food','0000-00-00','0000-00-00','2023-03-29 10:59:54',''),(15,'responsive-food,mobile_main_visual,set_img_after','841128783.jpg','1898236039.jpg','','','_none','Y','Title','','','',1,'Y','','responsive-food','0000-00-00','0000-00-00','2023-03-29 10:59:54',''),(19,'responsive-food,mobile_product_middle,set_img_after','3475737037.jpg','2532820638.jpg','','','_none','Y','이벤트','','','',0,'Y','','responsive-food','0000-00-00','0000-00-00','2023-03-29 10:59:54',NULL),(20,'responsive-food,mailing,not_set_view,not_set_term,not_set_link_target,not_set_banner_link','3786298713.jpg','','','','_none','Y','Title','','','',0,'Y','','responsive-food','0000-00-00','0000-00-00','2023-03-29 10:59:54',''),(41,'responsive-food,main_top,set_color,set_img_after','2495496195.jpg','4188603820.jpg','','','_none','Y','Title','','','',0,'Y','#222','responsive-food','0000-00-00','0000-00-00','2023-03-29 10:59:54',''),(43,'responsive-food,main_middle2,set_detail_text,set_linkmore_btn','125528494.jpg','1324180743.jpg','','','_none','Y','Title','','','내용을 입력해주세요',1,'Y','','responsive-food','0000-00-00','0000-00-00','2023-03-29 10:59:54',''),(62,'responsive-golf,mobile_main_visual,set_img_after','791374844.jpg','2990744149.jpg','/?pn=product.view&pcode=X8000-P0254-T3646','','_self','Y','Go beyond the limit of speed faster, farther. ONE iron','','','',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 16:27:43',NULL),(63,'responsive-golf,mobile_main_visual,set_img_after','1610492513.jpg','1828715510.jpg','','','_none','Y','Go beyond the limit of speed faster, farther. ONE iron','','','',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 16:27:58',NULL),(50,'responsive-food,main_middle2,set_detail_text,set_linkmore_btn','1610135994.jpg','3994297429.jpg','','자세히 보기','_none','Y','Title','','','내용을 입력해주세요',0,'Y','','responsive-food','0000-00-00','0000-00-00','2023-03-29 11:08:31',''),(49,'responsive-food,main_middle1,set_img_after','3186935592.jpg','','','','_none','Y','Title','','','',0,'Y','','responsive-food','0000-00-00','0000-00-00','2023-03-29 11:01:45',''),(59,'responsive-golf,main_top,set_color,set_img_after','2239023506.jpg','14359711.jpg','','','_none','Y','쇼핑몰 APP 다운로드시 10% 할인쿠폰','','','',0,'Y','#222','responsive-golf','0000-00-00','0000-00-00','2023-03-29 14:21:50',NULL),(60,'responsive-golf,mobile_top_logo','2866471992.png','','','','_none','Y','ONEgolf','','','',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 14:40:33','1159358658.png'),(61,'responsive-golf,mobile_main_visual,set_img_after','1787754427.jpg','2934978912.jpg','','','_none','Y','Go beyond the limit of speed faster, farther. ONE iron','','','',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 16:25:45',NULL),(67,'responsive-golf,main_middle1,set_detail_text,set_img_after,set_linkmore_btn','3759300073.jpg','588249178.jpg','/','Read More','_self','Y','Be the ONE','','','We are communicating\r\n with customers who are thinking about\r\nstarting an online business.',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 16:03:40',''),(68,'responsive-golf,main_middle1,set_detail_text,set_img_after,set_linkmore_btn','1691194555.jpg','','','','_none','N','2023  Best 골프용품 총집합!','','','ONEgolf 티칭프로와 함께하는\r\n프리미엄 18홀 라운딩을 경험하세요!',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 16:04:05',''),(69,'responsive-golf,main_middle2,set_detail_text,set_linkmore_btn','3409574982.jpg','','/?pn=product.view&pcode=A2668-K4581-X3347','Read More','_self','Y','Golf Best 2022','','','Go beyond the limit of speed\r\nfaster, farther. For Golfer',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 16:05:26',''),(70,'responsive-golf,main_middle2,set_detail_text,set_linkmore_btn','1977923642.jpg','','/?pn=product.view&pcode=P9352-A0491-I1021','이벤트 더알아보기','_self','Y','지금 예약하면 30%  페이백','','','기간내 사전예약시 30% 할인 혜택을 드립니다.\r\n지금 바로 신청하세요!',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 16:06:19',''),(71,'responsive-golf,main_middle2,set_detail_text,set_linkmore_btn','1250906713.jpg','','','','_none','Y','Golf Best 2023','','','Go beyond the limit of speed\r\nfaster, farther. For Golfer',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 10:29:48',''),(72,'responsive-golf,main_mdpick','2977958302.jpg','','','','_none','Y','Golf Md2023	','','','',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 14:30:51',''),(73,'responsive-golf,main_mdpick','1633176106.jpg','','/','','_self','Y','Golf Md2023  2','','','',0,'Y','','responsive-golf','0000-00-00','0000-00-00','2023-03-29 14:31:39','');
/*!40000 ALTER TABLE `smart_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_baro_cashbill`
--

DROP TABLE IF EXISTS `smart_baro_cashbill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_baro_cashbill` (
  `bc_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `bc_type` enum('barobill','pg') NOT NULL DEFAULT 'pg' COMMENT '발행구분 (바로빌발행, PG사발행(조회만가능))',
  `bc_ordernum` varchar(20) NOT NULL COMMENT '연동주문번호',
  `bc_isdelete` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '삭제여부',
  `bc_iscancel` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '취소여부',
  `MgtKey` varchar(50) NOT NULL COMMENT '연동사부여문서키',
  `TradeType` enum('N','D') NOT NULL DEFAULT 'N' COMMENT '거래구분 : N-승인거래, D-취소거래',
  `TradeUsage` enum('1','2') NOT NULL DEFAULT '1' COMMENT '거래용도 1 : ''소득공제'', 2 : ''지출증빙 (사업자번호)''',
  `TradeMethod` enum('1','2','3','4','5') NOT NULL DEFAULT '4' COMMENT '신분확인번호 구분',
  `IdentityNum` varchar(50) NOT NULL COMMENT '신분확인번호',
  `Amount` int(11) NOT NULL DEFAULT 0 COMMENT '공급가액',
  `Tax` int(11) NOT NULL DEFAULT 0 COMMENT '부가세',
  `ServiceCharge` int(11) NOT NULL DEFAULT 0 COMMENT '봉사료',
  `ItemName` varchar(100) NOT NULL DEFAULT '미기재' COMMENT '품목명',
  `Email` varchar(50) NOT NULL COMMENT '소비자이메일',
  `HP` varchar(30) NOT NULL COMMENT '소비자휴대폰번호(미사용)',
  `Memo` text NOT NULL COMMENT '관리자메모',
  `BarobillState` varchar(10) NOT NULL COMMENT '바로빌 상태',
  `TradeDate` date NOT NULL COMMENT '거래일자 - yyyyMMdd 형식',
  `RegistDT` datetime NOT NULL COMMENT '등록일시 - yyyyMMddHHmmss 형식',
  `IssueDT` datetime NOT NULL COMMENT '발행일시 - yyyyMMddHHmmss 형식',
  `NTSConfirmNum` varchar(10) NOT NULL COMMENT '국세청 승인번호	',
  `NTSSendDT` datetime NOT NULL COMMENT '국세청 전송일시 - yyyyMMddHHmmss 형식',
  `NTSConfirmDT` datetime NOT NULL COMMENT '국세청 승인일시 - yyyyMMddHHmmss 형식',
  `NTSConfirmMessage` varchar(100) NOT NULL COMMENT '국세청 전송결과	 - 전송성공인경우에만 확인가능',
  `CancelType` varchar(1) NOT NULL COMMENT '취소사유 - 1:거래취소 2:오류발급 3:기타',
  `CancelNTSConfirmNum` varchar(10) NOT NULL COMMENT '취소원본 국세청 승인번호',
  `CancelNTSConfirmDate` date NOT NULL COMMENT '취소원본 국세층 승인일자',
  PRIMARY KEY (`bc_uid`),
  KEY `bc_type` (`bc_type`,`MgtKey`),
  KEY `bc_ordernum` (`bc_ordernum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='바로빌 현금영수증 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_baro_cashbill`
--

LOCK TABLES `smart_baro_cashbill` WRITE;
/*!40000 ALTER TABLE `smart_baro_cashbill` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_baro_cashbill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_baro_tax`
--

DROP TABLE IF EXISTS `smart_baro_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_baro_tax` (
  `bt_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `bt_total_price` int(11) NOT NULL COMMENT '발행기준금액(공급가액+세액)',
  `bt_rdate` datetime NOT NULL COMMENT '세금계산서 작성일',
  `bt_idate` datetime NOT NULL COMMENT '세금계산서 발행일',
  `bt_cdate` datetime NOT NULL COMMENT '세금계산서 취소일',
  `bt_is_delete` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '삭제여부',
  `TaxInvoiceType` tinyint(1) NOT NULL DEFAULT 1 COMMENT '계산서종류(1-세금계산서(과세,영세), 2-계산서(면세), 4-위수탁세금계산서, 5-위수탁계산서)',
  `MgtKey` varchar(30) NOT NULL COMMENT '연동사부여 문서키',
  `CorpNum` varchar(30) NOT NULL COMMENT '사업자번호',
  `CorpName` varchar(100) NOT NULL COMMENT '회사명',
  `CEOName` varchar(30) NOT NULL COMMENT '대표자명',
  `Addr` varchar(255) NOT NULL COMMENT '주소',
  `BizType` varchar(40) NOT NULL COMMENT '업종',
  `BizClass` varchar(40) NOT NULL COMMENT '업태',
  `ContactName` varchar(30) NOT NULL COMMENT '담당자명',
  `TEL` varchar(20) NOT NULL COMMENT '전화번호',
  `HP` varchar(20) NOT NULL COMMENT '휴대폰',
  `Email` varchar(50) NOT NULL COMMENT '이메일',
  `Name` varchar(100) NOT NULL COMMENT '품목',
  `UnitPrice` int(11) NOT NULL COMMENT '단가',
  `Amount` int(11) NOT NULL COMMENT '공급가액',
  `Tax` int(11) NOT NULL COMMENT '세액',
  `Status` varchar(30) NOT NULL COMMENT '발행상태',
  `bt_suid` int(11) NOT NULL COMMENT '정산내역 연동 uid',
  PRIMARY KEY (`bt_uid`),
  KEY `MgtNum` (`MgtKey`,`CorpNum`,`Status`),
  KEY `bt_is_delete` (`bt_is_delete`),
  KEY `bt_suid` (`bt_suid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='바로빌 세금계산서 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_baro_tax`
--

LOCK TABLES `smart_baro_tax` WRITE;
/*!40000 ALTER TABLE `smart_baro_tax` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_baro_tax` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_baro_tax_log`
--

DROP TABLE IF EXISTS `smart_baro_tax_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_baro_tax_log` (
  `tl_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tl_btuid` int(10) NOT NULL DEFAULT 0 COMMENT '연동정보 고유번호',
  `tl_mode` varchar(20) NOT NULL DEFAULT 'regist' COMMENT '연동모드 - regist:임시저장, issue:발행, cancel:발행취소, delete:삭제',
  `tl_code` varchar(20) NOT NULL COMMENT '성고 또는 오류코드',
  `tl_remark` varchar(255) DEFAULT NULL COMMENT '비고사항 : 오류코드 - 또는 오류내용',
  `tl_rdate` datetime DEFAULT NULL,
  PRIMARY KEY (`tl_uid`),
  KEY `tl_suid` (`tl_btuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='바로빌 세금계선서 로그관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_baro_tax_log`
--

LOCK TABLES `smart_baro_tax_log` WRITE;
/*!40000 ALTER TABLE `smart_baro_tax_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_baro_tax_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_bbs`
--

DROP TABLE IF EXISTS `smart_bbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_bbs` (
  `b_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `b_inid` varchar(50) NOT NULL COMMENT '등록한 개인회원',
  `b_writer` varchar(50) NOT NULL DEFAULT '' COMMENT '작성자명',
  `b_passwd` varchar(50) NOT NULL DEFAULT '' COMMENT '글 비밀번호 - 비회원 전용',
  `b_secret` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '비밀글 여부',
  `b_menu` varchar(50) NOT NULL DEFAULT 'story' COMMENT '게시판 종류 : interior-원룸인테리어 ,cook-싱글밥상/요리 ,tour-여행/숙박 ,friend-여친! 남친! ,music-음악/영화 ,fashion-패션/쇼핑 ,story-싱글스토리',
  `b_sdate` date DEFAULT NULL COMMENT '시작일',
  `b_edate` date DEFAULT NULL COMMENT '종료일',
  `b_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `b_title` varchar(100) NOT NULL COMMENT '문의제목',
  `b_content` text DEFAULT NULL COMMENT '내용',
  `b_img1` varchar(50) DEFAULT NULL COMMENT '등록한 이미지1',
  `b_img2` varchar(50) DEFAULT NULL COMMENT '등록한 이미지2',
  `b_img1_loc` enum('top','bottom') DEFAULT 'top' COMMENT '[미사용]등록한 이미지1의 위치',
  `b_img2_loc` enum('top','bottom') DEFAULT 'top' COMMENT '[미사용]등록한 이미지2의 위치',
  `b_rdate` datetime DEFAULT NULL COMMENT '등록일',
  `b_mdate` datetime DEFAULT NULL COMMENT '수정일',
  `b_recomm` int(10) unsigned DEFAULT 0 COMMENT '[미사용]공감 갯수',
  `b_hit` int(10) unsigned DEFAULT 0 COMMENT '조회 갯수',
  `b_talkcnt` int(10) unsigned DEFAULT 0 COMMENT '덧글 갯수',
  `b_shorten_url` varchar(50) DEFAULT NULL COMMENT '[미사용]고유한 짧은 url',
  `b_notice` char(1) NOT NULL COMMENT '공지여부',
  `b_writer_type` enum('member','guest','admin') NOT NULL DEFAULT 'member' COMMENT '작성자 유형 - 회원/비회원',
  `b_depth` tinyint(2) NOT NULL DEFAULT 1 COMMENT '답글 depth 형태, 부모글 1, 자식글 2',
  `b_relation` int(10) NOT NULL DEFAULT 0 COMMENT 'b_depth가 2일 경우 부모글의 b_uid',
  `b_best` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '베스트글 사용여부',
  `b_reginfo_ip` varchar(255) DEFAULT NULL COMMENT '등록자 아이피',
  `b_editor_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '등록당시 에디터로 등록하였는지 여부 (Y,N)',
  `b_category` varchar(30) NOT NULL COMMENT '게시판 카테고리',
  PRIMARY KEY (`b_uid`),
  KEY `b_menu` (`b_menu`),
  KEY `b_inid` (`b_inid`),
  KEY `b_depth` (`b_depth`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COMMENT='게시판관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_bbs`
--

LOCK TABLES `smart_bbs` WRITE;
/*!40000 ALTER TABLE `smart_bbs` DISABLE KEYS */;
INSERT INTO `smart_bbs` VALUES (27,'master','관리자','','N','news',NULL,NULL,'','뉴스 제목입니다.','<p>뉴스 내용입니다.</p>','286380140.jpg',NULL,'top','top','2023-03-29 18:18:13','2023-03-29 00:00:00',0,43,0,NULL,'','admin',1,0,'N','112.219.125.10','Y','뉴스카테고리'),(10,'master','관리자','','N','event','2023-03-30','2024-03-30','','회원가입 이벤트! 누구나 참여 가능하고 많은 경품이 기다리고 있어요.','<p>두근두근 첫맛남! 회원가입을 하면 경품이 와르르~!</p><p><br></p><p>지금 바로 참여하세요</p>','1867338054.jpg',NULL,'top','top','2023-03-24 09:56:55','2023-03-27 00:00:00',0,78,0,NULL,'','admin',1,0,'N','112.219.125.10','Y','적립이벤트'),(66,'master','관리자','*BE1BDEC0AA74B4DCB079943E70528096CCA985F8','N','notice',NULL,NULL,'','회원가입시 지급되는 포인트 안내사항입니다.','<p>내용입니다.<br></p>',NULL,NULL,'top','top','2023-03-20 11:53:49',NULL,0,2,0,NULL,'','admin',1,0,'N','112.219.125.10','Y',''),(11,'master','관리자','','N','event','2023-03-30','2024-03-30','','이벤트 제목입니다.','<p>이벤트 내용입니다.<br></p>','2895211605.jpg',NULL,'top','top','2023-03-24 10:00:28','2023-03-24 00:00:00',0,147,0,NULL,'','admin',1,0,'N','112.219.125.10','Y','할인이벤트'),(20,'master','관리자','','N','notice',NULL,NULL,'','홈페이지가 새롭게 오픈했습니다. 많은 관심과 사랑 부탁드려요! 기획전과 이벤트에 다양한 혜택을 올릴 예정이오니 많이 사랑과 관심 부탁드립니다','<p>안녕하세요</p><p><br></p><p>홈페이지가 새롭 오픈했습니다. 많은 관심과 사랑 부탁드려요!<br></p><p><br></p>',NULL,NULL,'top','top','2023-03-29 14:54:40','2023-03-29 00:00:00',0,314,0,NULL,'','admin',1,0,'N','112.219.125.10','Y',''),(26,'master','관리자','','N','news',NULL,NULL,'','뉴스 제목입니다.','<p>뉴스 내용입니다.<br></p>','3637935485.jpg',NULL,'top','top','2023-03-29 18:16:04','2023-03-29 00:00:00',0,21,0,NULL,'','admin',1,0,'N','112.219.125.10','Y',''),(67,'master','관리자','*BE1BDEC0AA74B4DCB079943E70528096CCA985F8','N','notice',NULL,NULL,'','봄맞이 이벤트를 준비중입니다!','<p>내용입니다.</p>',NULL,NULL,'top','top','2023-03-20 11:54:28',NULL,0,5,0,NULL,'','admin',1,0,'N','112.219.125.10','Y',''),(54,'master','관리자','','N','notice',NULL,NULL,'','휴무일 공지해드립니다.','<p>공지사항<br></p>',NULL,NULL,'top','top','2023-01-20 14:58:15','2023-01-20 00:00:00',0,18,0,NULL,'','admin',1,0,'N','112.219.125.10','Y',''),(56,'master','관리자','*BE1BDEC0AA74B4DCB079943E70528096CCA985F8','N','notice',NULL,NULL,'','신제품 출시! 많은 관심 부탁드립니다.','<p>일반양식<br style=\\\"clear:both;\\\">&nbsp;</p>',NULL,NULL,'top','top','2023-03-11 22:38:13','2023-03-11 00:00:00',0,20,1,NULL,'Y','admin',1,0,'N','112.219.125.10','Y','');
/*!40000 ALTER TABLE `smart_bbs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_bbs_comment`
--

DROP TABLE IF EXISTS `smart_bbs_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_bbs_comment` (
  `bt_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bt_buid` int(10) unsigned NOT NULL COMMENT '게시물 고유번호',
  `bt_writer` varchar(50) NOT NULL COMMENT '작성자 명',
  `bt_rdate` datetime DEFAULT NULL COMMENT '등록일',
  `bt_inid` varchar(50) NOT NULL COMMENT '등록한 개인회원',
  `bt_content` text DEFAULT NULL COMMENT '리뷰내용',
  `bt_depth` tinyint(2) DEFAULT 1 COMMENT '부모 , 자식글 구분',
  `bt_relation` int(10) DEFAULT 0 COMMENT '부모글 bt_uid, 자식글이 아니면 0',
  `bt_reginfo_ip` varchar(255) DEFAULT NULL COMMENT '등록자아이피',
  PRIMARY KEY (`bt_uid`),
  KEY `bt_cpid` (`bt_buid`),
  KEY `bt_depth` (`bt_depth`),
  KEY `bt_relation` (`bt_relation`),
  KEY `bt_inid` (`bt_inid`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='게시판 덧글 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_bbs_comment`
--

LOCK TABLES `smart_bbs_comment` WRITE;
/*!40000 ALTER TABLE `smart_bbs_comment` DISABLE KEYS */;
INSERT INTO `smart_bbs_comment` VALUES (37,56,'관리자','2023-03-15 11:33:14','master','안녕하세요. 출시 일정은 추가 공지하겠습니다. 감사합니다.',1,0,'112.219.125.10');
/*!40000 ALTER TABLE `smart_bbs_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_bbs_faq`
--

DROP TABLE IF EXISTS `smart_bbs_faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_bbs_faq` (
  `bf_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bf_type` varchar(30) DEFAULT NULL COMMENT 'faq타입',
  `bf_title` varchar(300) NOT NULL COMMENT 'FAQ제목',
  `bf_content` text DEFAULT NULL COMMENT '질문내용',
  `bf_best` char(1) NOT NULL COMMENT '공지여부(체크시 Y)',
  `bf_rdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`bf_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='FAQ게시판';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_bbs_faq`
--

LOCK TABLES `smart_bbs_faq` WRITE;
/*!40000 ALTER TABLE `smart_bbs_faq` DISABLE KEYS */;
INSERT INTO `smart_bbs_faq` VALUES (1,'2','비밀번호나 주소, 연락처는 어디에서 변경하나요?','<p>사이트 상단 오른쪽 마이페이지&gt;회원정보수정에서 변경해주시면 됩니다.&nbsp;<br></p>','Y','2023-01-31 14:38:18'),(53,'2','회원으로 가입해야만 주문이 가능한가요?','<p>상품 정보페이지에서 구매하기을 클릭 후 \\\'비회원 주문하기\\\'를 이용하시면 됩니다.&nbsp;</p><p>단, 일부 청소년유해매체 상품 등은 주문이 불가능합니다.</p><p><br></p><p>비회원 주문시 이벤트 및 적립금과 할인쿠폰 등의 혜택은 제외됩니다.&nbsp;</p>','Y','2023-01-31 15:22:37'),(54,'2','30만원 이상 결제시 공인인증서 인증이 필수인가요?','<p>2005년 11월 1일부터 금감원의 전자상거래 안정성 강화 정책에 따라 30만원 이상의 모든 신용카드 결제에 공인인증 사용이 의무화 되었습니다.</p><p><br></p><p>단, 30만원 이하 결제시는 공인인증서 없어도 결제가 가능합니다.&nbsp;</p>','Y','2023-01-31 15:23:06'),(55,'3','상품에 대한 문의는 어떻게 하나요?','<p>상품에 대한 문의는 고객센터 상품문의를 이용하시면, 고객센터나 판매업체에서 답변을 드립니다.&nbsp;</p><p>또한 상품페이지 하단의 상품Q/A에 등록하시면 다른 고객들과 의견을 나누실 수 있습니다.&nbsp;</p>','Y','2023-01-31 15:23:30'),(56,'3','배송비는 어떻게 되나요?','<p>상품별 단품페이지에서 상품의 배송비를 확인하실 수 있습니다.</p><p>&nbsp;</p><p>무료배송 상품의 경우 결제시 별도로 결제할 비용이 없으나, 유료배송 상품의 경우 결제시 배송비를 함께 결제하셔야 합니다.</p><p>(단, 유료배송 상품이더라도 묶음배송을 통해 배송비 기준 금액을 초과하여 구매하실 경우 무료배송으로 변경됩니다.)</p><p>&nbsp;</p><p>단, 일부 상품의 경우 지역이나 배송조건에 따라 추가 비용이 발생할 수 있습니다.&nbsp;</p>','Y','2023-01-31 15:23:49');
/*!40000 ALTER TABLE `smart_bbs_faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_bbs_info`
--

DROP TABLE IF EXISTS `smart_bbs_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_bbs_info` (
  `bi_uid` char(20) NOT NULL,
  `bi_name` varchar(50) NOT NULL DEFAULT '' COMMENT '게시판이름',
  `bi_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '노출여부',
  `bi_view_type` enum('service','community') NOT NULL DEFAULT 'service' COMMENT '게시판 노출 유형 (고객센터,커뮤니티) - var.php 참조',
  `bi_list_type` varchar(30) NOT NULL DEFAULT 'board' COMMENT '게시판 유형 - board,gallery,event,qna,basic,notice',
  `bi_auth_list` int(11) NOT NULL DEFAULT 0,
  `bi_auth_view` int(11) NOT NULL DEFAULT 0,
  `bi_auth_write` int(11) NOT NULL DEFAULT 0,
  `bi_auth_reply` int(11) NOT NULL DEFAULT 0,
  `bi_auth_comment` int(11) NOT NULL DEFAULT 0,
  `bi_auth_editor` int(11) NOT NULL DEFAULT 9 COMMENT '에디터권한 권한',
  `bi_auth_editor_group` text DEFAULT NULL COMMENT '에디터 사용이 회원일 시 처리 ',
  `bi_recaptcha_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '리캡챠 사용여부 :: Y,N ',
  `bi_recaptcha_set` enum('all','nonemember') NOT NULL DEFAULT 'all' COMMENT '리캡챠 적용대상 :: all(회원+비회원), nonemember(비회원)',
  `bi_listmaxcnt` int(11) NOT NULL DEFAULT 20 COMMENT '페이지당게시물 갯수',
  `bi_newicon_view` int(11) NOT NULL DEFAULT 7 COMMENT 'new 아이콘 노출 기간(일)',
  `bi_comment_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '댓글기능사용여부',
  `bi_file_upload_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '파일업로드기능 사용여부',
  `bi_images_upload_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '이미지 첨부기능 사용여부',
  `bi_file_size_limit` int(11) NOT NULL DEFAULT 1000000 COMMENT '업로드 용량제한 Byte',
  `bi_secret_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '비밀글 사용여부',
  `bi_html_header` text NOT NULL COMMENT '게시판 상단에 넣을 html',
  `bi_html_footer` text NOT NULL COMMENT '게시판 하단에 넣을 html',
  `bi_post_cnt` int(11) NOT NULL DEFAULT 0 COMMENT '게시물 갯수',
  `bi_view_idx` int(5) unsigned NOT NULL DEFAULT 1 COMMENT '게시판노출순서',
  `bi_skin` varchar(50) NOT NULL DEFAULT 'basic' COMMENT '게시판 스킨 PC',
  `bi_skin_m` varchar(50) NOT NULL DEFAULT 'basic' COMMENT '게시판 스킨 mobile',
  `bi_auth_list_group` text DEFAULT NULL COMMENT '리스트권한 회원일 시 그룹아이디(,로구분)',
  `bi_auth_view_group` text DEFAULT NULL COMMENT '보기권한 회원일 시 그룹아이디(,로구분)',
  `bi_auth_write_group` text DEFAULT NULL COMMENT '쓰기권한 회원일 시 그룹아이디(,로구분)',
  `bi_auth_reply_group` text DEFAULT NULL COMMENT '답글권한 회원일 시 그룹아이디(,로구분)',
  `bi_auth_comment_group` text DEFAULT NULL COMMENT '댓글권한 회원일 시 그룹아이디(,로구분)',
  `bi_reply_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '답글사용유무(Y,N)',
  `bi_writer_view_use` enum('Y','N') NOT NULL DEFAULT 'N',
  `bi_write_day_use` enum('Y','N') NOT NULL DEFAULT 'N',
  `bi_write_day_cnt` int(11) NOT NULL,
  `bi_rdate` datetime NOT NULL,
  `bi_option_date_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '기간이벤트사용여부',
  `bi_btuid` int(11) DEFAULT NULL COMMENT '쇼핑물 게시글 양식 고유번호',
  `bi_category_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '게시판 카테고리 사용여부',
  `bi_category` varchar(255) NOT NULL COMMENT '게시판 카테고리',
  `bi_main_view` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '고객센터 게시판 메인노출여부(''Y:메인노출'',''N:메인노출x'')',
  PRIMARY KEY (`bi_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='게시판 설정 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_bbs_info`
--

LOCK TABLES `smart_bbs_info` WRITE;
/*!40000 ALTER TABLE `smart_bbs_info` DISABLE KEYS */;
INSERT INTO `smart_bbs_info` VALUES ('notice','공지사항','Y','service','notice',0,0,0,0,2,9,'1,5','Y','nonemember',20,5,'Y','Y','N',0,'Y','','',5,14,'notice','notice','1,5','1,5','','1','1,5','N','Y','Y',5,'2023-01-01 09:22:51','N',4,'N','상품,기타','Y'),('event','이벤트','Y','community','event',0,0,0,0,2,9,'','Y','all',20,5,'Y','Y','Y',0,'Y','','',2,17,'event','event','','','1','','1,5','N','N','N',0,'2023-01-01 09:22:51','Y',0,'Y','할인이벤트,적립이벤트','N'),('freeboard','자유게시판','Y','community','basic',0,0,0,0,2,9,'','Y','all',20,5,'Y','Y','N',0,'Y','','',0,14,'basic','basic','','','','','','Y','N','N',0,'2023-01-01 09:22:51','N',4,'Y','즐거운이야기,나눔장터','N'),('qna','질문과답변','Y','service','qna',0,0,0,0,2,9,'1,4,5','Y','all',20,0,'N','N','N',0,'Y','','',0,11,'qna','qna','1,4,5','1,4,5','1,4,5','1,4,5','1,4,5','Y','Y','N',20,'2023-01-01 09:22:51','N',0,'Y','상품문의,주문문의,기타','N'),('news','뉴스','Y','service','news',0,0,0,0,2,9,'','Y','all',20,5,'Y','N','Y',0,'N','','',2,13,'news','news','1,5','1,5','1,5','1,5','1,5','N','N','N',20,'2023-02-19 11:20:40','N',0,'Y','뉴스카테고리,카테고리','N'),('gallery','갤러리','Y','community','gallery',0,0,0,2,2,9,'','N','all',20,0,'Y','N','Y',0,'N','','',0,16,'gallery','gallery','1,5','1,5','1,5','1,5','1,5','N','N','N',20,'2023-03-24 10:55:04','N',0,'N','','N');
/*!40000 ALTER TABLE `smart_bbs_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_bbs_template`
--

DROP TABLE IF EXISTS `smart_bbs_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_bbs_template` (
  `bt_uid` int(11) NOT NULL AUTO_INCREMENT,
  `bt_type` enum('shop','admin') NOT NULL DEFAULT 'shop' COMMENT '양식타입 (normal:일반 양식, admin: 관리자 양식)',
  `bt_title` varchar(512) NOT NULL COMMENT '제목',
  `bt_content` text DEFAULT NULL COMMENT '내용',
  `bt_rdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`bt_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='게시글 양식';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_bbs_template`
--

LOCK TABLES `smart_bbs_template` WRITE;
/*!40000 ALTER TABLE `smart_bbs_template` DISABLE KEYS */;
INSERT INTO `smart_bbs_template` VALUES (1,'admin','문의 답변형식','관리자양식','2023-01-19 15:56:06'),(4,'shop','소비자 문의형식','아래와 같이 질문을 작성해주세요\r\n===================================\r\n성함 : \r\n제품명 : \r\n구매시기 : \r\n문의사항:\r\n===================================\r\n감사합니다.','2023-01-19 16:22:00');
/*!40000 ALTER TABLE `smart_bbs_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_brand`
--

DROP TABLE IF EXISTS `smart_brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_brand` (
  `c_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_name` varchar(50) DEFAULT NULL COMMENT '브랜드명',
  `c_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '노출여부',
  `c_img` varchar(50) DEFAULT NULL COMMENT '브랜드 이미지',
  `c_depth` tinyint(2) unsigned NOT NULL DEFAULT 1 COMMENT '브랜드 depth, 1부터 시작',
  `c_idx` int(5) NOT NULL DEFAULT 0 COMMENT '브랜드 순서, 같은 부모 브랜드에서만 유효함',
  `c_parent` varchar(200) DEFAULT NULL COMMENT '부모 브랜드',
  `c_pro_cnt` int(11) NOT NULL DEFAULT 0 COMMENT '상품수',
  `c_prefix_str` varchar(10) NOT NULL COMMENT '브랜드 초성 정보',
  PRIMARY KEY (`c_uid`),
  KEY `c_view` (`c_view`),
  KEY `c_depth` (`c_depth`),
  KEY `c_parent` (`c_parent`),
  KEY `c_prefix_str` (`c_prefix_str`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='브랜드 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_brand`
--

LOCK TABLES `smart_brand` WRITE;
/*!40000 ALTER TABLE `smart_brand` DISABLE KEYS */;
INSERT INTO `smart_brand` VALUES (24,'DEF','Y',NULL,1,1,NULL,0,'D'),(34,'MNO','Y',NULL,1,1,NULL,0,'M'),(26,'아자차','Y',NULL,1,1,NULL,0,'ㅇ'),(22,'마바사','Y',NULL,1,1,NULL,0,'ㅁ'),(23,'GHI','Y',NULL,1,1,NULL,0,'G'),(27,'타파하','Y',NULL,1,1,NULL,0,'ㅌ'),(28,'365BRAND','Y',NULL,1,1,NULL,0,'3'),(30,'ABC','Y',NULL,1,1,NULL,0,'A'),(33,'JKL','Y',NULL,1,1,NULL,0,'J'),(32,'가나다','Y',NULL,1,1,NULL,0,'ㄱ'),(36,'GOGO','Y',NULL,1,1,NULL,0,'G');
/*!40000 ALTER TABLE `smart_brand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cart`
--

DROP TABLE IF EXISTS `smart_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cart` (
  `c_uid` int(10) NOT NULL AUTO_INCREMENT,
  `c_cookie` varchar(50) NOT NULL DEFAULT '' COMMENT '접속COOKIE코드',
  `c_pcode` varchar(20) NOT NULL DEFAULT '' COMMENT '상품고유코드',
  `c_pouid` int(10) unsigned NOT NULL COMMENT '옵션고유번호 - 3차일 경우 3차 옵션 uid',
  `c_option1` varchar(100) DEFAULT NULL COMMENT '선택 옵션1',
  `c_option2` varchar(100) DEFAULT NULL COMMENT '선택 옵션2',
  `c_option3` varchar(100) DEFAULT NULL COMMENT '선택 옵션3',
  `c_cnt` int(5) DEFAULT NULL COMMENT '수량',
  `c_supply_price` int(10) NOT NULL DEFAULT 0 COMMENT '공급가',
  `c_price` int(10) NOT NULL DEFAULT 0 COMMENT '할인가 - 실판매가',
  `c_point` float NOT NULL DEFAULT 0 COMMENT '적용포인트량',
  `c_rdate` datetime DEFAULT NULL COMMENT '등록일시',
  `c_direct` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '바로구매 (Y:바로구매,N:장바구니구매)',
  `c_is_addoption` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '추가옵션여부',
  `c_addoption_parent` int(11) NOT NULL COMMENT '추가옵션일때 필수옵션 uid',
  `c_old_price` int(11) NOT NULL COMMENT '기존가격(할인있을경우 다를 수 있다)',
  `c_old_point` int(11) NOT NULL COMMENT '기존포인트(할인있을경우 다를 수 있다)',
  `c_groupset_price_per` decimal(3,1) NOT NULL COMMENT '할인률(있을경우 0 이상)',
  `c_groupset_point_per` decimal(3,1) NOT NULL COMMENT '추가적립률(있을경우 0 이상)',
  `c_dateoption_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '달력옵션 사용여부(Y:사용,N:미사용)',
  `c_dateoption_date` date DEFAULT NULL COMMENT '달력옵션 사용시 선택된 날짜',
  PRIMARY KEY (`c_uid`),
  KEY `c_cookie` (`c_cookie`),
  KEY `c_pcode` (`c_pcode`),
  KEY `c_dateoption_use_c_dateoption_date` (`c_dateoption_use`,`c_dateoption_date`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='장바구니관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cart`
--

LOCK TABLES `smart_cart` WRITE;
/*!40000 ALTER TABLE `smart_cart` DISABLE KEYS */;
INSERT INTO `smart_cart` VALUES (12,'5e4c68084541b19f2b489f958e4e7968','D5568-V9700-L4557',0,NULL,NULL,NULL,1,9003,10003,0,'2023-11-24 15:54:46','N','N',0,10003,0,0.0,0.0,'N',NULL),(7,'3636f2610bf1250d828243ba3df47707','U8905-L6749-Y3508',0,NULL,NULL,NULL,1,9003,10003,0,'2023-04-24 15:20:58','N','N',0,10003,0,0.0,0.0,'N',NULL),(8,'master','A2939-W4908-C9360',0,NULL,NULL,NULL,1,0,10025,100,'2023-06-09 14:05:56','Y','N',0,10025,100,0.0,0.0,'N',NULL);
/*!40000 ALTER TABLE `smart_cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_category`
--

DROP TABLE IF EXISTS `smart_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_category` (
  `c_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_name` varchar(50) DEFAULT NULL COMMENT '카테고리명',
  `c_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '노출여부',
  `c_img` varchar(50) DEFAULT NULL COMMENT '카테고리 이미지',
  `c_img_on` varchar(50) NOT NULL COMMENT '카테고리 이미지 (마우스오버시)',
  `c_img_top_banner` varchar(50) NOT NULL DEFAULT '' COMMENT '상단 탑배너',
  `c_depth` tinyint(2) unsigned NOT NULL DEFAULT 1 COMMENT '카테고리 depth, 1부터 시작',
  `c_idx` int(5) NOT NULL DEFAULT 0 COMMENT '카테고리 순서, 같은 부모 카테고리에서만 유효함',
  `c_parent` varchar(200) DEFAULT NULL COMMENT '부모 카테고리',
  `c_pro_cnt` int(11) NOT NULL DEFAULT 0 COMMENT '상품수',
  `c_img_top_banner_target` enum('_none','_self','_blank') NOT NULL DEFAULT '_self' COMMENT '배너 링크 타켓',
  `c_img_top_banner_link` varchar(200) NOT NULL DEFAULT '' COMMENT '배너 링크',
  `c_img_top_banner_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상단배너 활성화여부',
  `c_img_top_mobile_banner` varchar(50) NOT NULL COMMENT '카테고리 배너 이미지(모바일)',
  `c_img_top_mobile_banner_target` enum('_none','_self','_blank') NOT NULL DEFAULT '_self' COMMENT '카테고리배너 링크 타겟(모바일)',
  `c_img_top_mobile_banner_link` varchar(200) NOT NULL COMMENT '카테고리 배너 링크(모바일)',
  `c_img_top_mobile_banner_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '카테고리 배너 사용여부(모바일)',
  `c_best_product_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '베스트노출 여부',
  `c_best_product_display` int(11) NOT NULL DEFAULT 4 COMMENT '베스트 진열방식',
  `c_best_product_mobile_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '베스트노출 여부(모바일)',
  `c_best_product_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '베스트 진열방식(모바일)',
  `c_list_product_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '리스트노출 여부',
  `c_list_product_display` int(11) NOT NULL DEFAULT 5 COMMENT '리스트 진열방식',
  `c_list_product_mobile_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '리스트노출 여부(모바일)',
  `c_list_product_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '리스트 진열방식(모바일)',
  `c_list_limit` int(10) NOT NULL DEFAULT 24 COMMENT '카테고리별 한페이지에 보여질 상품개수 기본 24개로 지정',
  `c_list_product_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '목록 카테고리 진열설정 (box 박스형,list 리스트형)',
  `c_best_product_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '베스트 카테고리 진열설정 (box 박스형,list 리스트형)',
  PRIMARY KEY (`c_uid`),
  KEY `c_view` (`c_view`),
  KEY `c_depth` (`c_depth`),
  KEY `c_parent` (`c_parent`)
) ENGINE=MyISAM AUTO_INCREMENT=520 DEFAULT CHARSET=utf8 COMMENT='카테고리 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_category`
--

LOCK TABLES `smart_category` WRITE;
/*!40000 ALTER TABLE `smart_category` DISABLE KEYS */;
INSERT INTO `smart_category` VALUES (1,'DESSERT','Y','','','',1,14,'0',2,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,24,'box','box'),(2,'GIFT','Y','','','',1,16,'0',8,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,24,'box','box'),(144,'CAKE','Y','','','',1,13,'0',5,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(147,'BREAD','Y','','','',1,12,'0',4,'','','','','_self','','N','',5,'Y',3,'',4,'Y',2,20,'box','box'),(203,'BREAD · FOOD','Y','','','',2,2,'147',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(206,'SNACK','Y','','','',2,1,'147',1,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(446,'SALAD','Y',NULL,'','',2,3,'1',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(435,'SANDWICH','Y',NULL,'','',2,2,'1',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(217,'DONUT','Y','','','',2,3,'147',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(447,'ETC 기타','Y',NULL,'','',1,17,'0',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,24,'box','box'),(492,'CHOCOLATE · CANDY','Y',NULL,'','',2,4,'447',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(449,'FRESH CREAM','Y',NULL,'','',2,1,'144',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(450,'MOUSSE','Y',NULL,'','',2,2,'144',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(451,'SPECIAL','Y',NULL,'','',2,3,'144',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(452,'GIFT','Y',NULL,'','',2,4,'144',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(489,'DESSERT','Y',NULL,'','',2,7,'144',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(488,'LARGE','Y',NULL,'','',2,6,'144',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(487,'SNACK','Y',NULL,'','',2,5,'144',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(491,'COOKED','Y',NULL,'','',2,6,'147',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(490,'EUROPEAN','Y',NULL,'','',2,5,'147',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(464,'PIE PASTRY','Y',NULL,'','',2,4,'147',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(468,'SOUP','Y',NULL,'','',2,4,'1',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(499,'FINISHED DRINK','Y',NULL,'','',3,6,'447,476',0,'_self','','N','','','','N','Y',4,'Y',3,'Y',4,'Y',1,0,'box','box'),(476,'DRINK','Y',NULL,'','',2,1,'447',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(477,'COOKIES','Y',NULL,'','',2,2,'447',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(478,'JAM · SPREAD','Y',NULL,'','',2,3,'447',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(498,'TEA & LATTE','Y',NULL,'','',3,5,'447,476',0,'_self','','N','','','','N','Y',4,'Y',3,'Y',4,'Y',1,0,'box','box'),(493,'ICE CREAM','Y',NULL,'','',2,5,'447',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(494,'COFFEE','Y',NULL,'','',3,1,'447,476',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,0,'box','box'),(495,'SMOOTHIE','Y',NULL,'','',3,2,'447,476',0,'_self','','N','','','','N','Y',4,'Y',3,'Y',4,'Y',1,0,'box','box'),(496,'SHAVED ICE','Y',NULL,'','',3,3,'447,476',0,'_self','','N','','','','N','Y',4,'Y',3,'Y',4,'Y',1,0,'box','box'),(497,'FRUIT JUICE & AID','Y',NULL,'','',3,4,'447,476',0,'_self','','N','','','','N','Y',4,'Y',3,'Y',4,'Y',1,0,'box','box'),(500,'샌드위치·샐러드','Y',NULL,'','',1,19,'0',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,24,'box','box'),(510,'ICE CREAM','Y',NULL,'','',1,18,'0',0,'','','','','_self','','N','',4,'Y',3,'',4,'Y',1,24,'box','box'),(511,'기프트세트','Y',NULL,'','',3,1,'144,449',0,'','','','','_self','','N','',4,'N',3,'',4,'Y',1,0,'box','box'),(512,'반값세일','Y',NULL,'','',3,2,'144,449',0,'_self','','N','','','','','Y',4,'N',3,'Y',4,'Y',1,0,'box','box'),(518,'FRESH CREAM','Y',NULL,'','',3,3,'144,449',0,'_self','','N','','','','','Y',4,'N',3,'Y',4,'Y',1,0,'box','box'),(519,'교환권','Y',NULL,'','',1,20,'0',0,'','','','','_self','','N','',0,'Y',0,'',5,'Y',2,24,'box','box');
/*!40000 ALTER TABLE `smart_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_age`
--

DROP TABLE IF EXISTS `smart_cntlog_age`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_age` (
  `sca_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '접속날짜',
  `sca_age` smallint(3) NOT NULL COMMENT '회원 연령',
  `sca_cnt_pc` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - PC',
  `sca_cnt_mo` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 모바일',
  `sca_cnt_app` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 앱',
  UNIQUE KEY `sca_unique` (`sca_date`,`sca_age`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속 - 회원연령 통계';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_age`
--

LOCK TABLES `smart_cntlog_age` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_age` DISABLE KEYS */;
INSERT INTO `smart_cntlog_age` VALUES ('2023-06-09',0,1,0,0),('2023-09-14',0,1,0,0),('2023-09-19',0,1,0,0);
/*!40000 ALTER TABLE `smart_cntlog_age` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_browser`
--

DROP TABLE IF EXISTS `smart_cntlog_browser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_browser` (
  `scb_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '접속날짜',
  `scb_browser` varchar(50) NOT NULL COMMENT '접속 브라우저',
  `scb_cnt_pc` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - PC',
  `scb_cnt_mo` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 모바일',
  `scb_cnt_app` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 앱',
  UNIQUE KEY `scb_unique` (`scb_date`,`scb_browser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속 - 브라우저 통계';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_browser`
--

LOCK TABLES `smart_cntlog_browser` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_browser` DISABLE KEYS */;
INSERT INTO `smart_cntlog_browser` VALUES ('2023-05-11','Chrome(Google Chrome)',2,1,0),('2023-05-11','Unknown(Unknown)',2,0,0),('2023-05-11','NateOn(NateOn)',2,0,0),('2023-05-11','Safari(Apple Safari)',0,1,0),('2023-05-15','Chrome(Google Chrome)',1,0,0),('2023-05-15','Unknown(Unknown)',2,0,0),('2023-05-16','Chrome(Google Chrome)',1,0,0),('2023-05-16','Unknown(Unknown)',2,0,0),('2023-05-17','Safari(Apple Safari)',0,2,0),('2023-05-17','Unknown(Unknown)',2,0,0),('2023-05-18','Chrome(Google Chrome)',1,0,0),('2023-05-18','Unknown(Unknown)',2,0,0),('2023-05-24','Chrome(Google Chrome)',1,0,0),('2023-05-24','Unknown(Unknown)',2,0,0),('2023-05-26','Chrome(Google Chrome)',1,0,0),('2023-05-26','Unknown(Unknown)',2,0,0),('2023-06-01','Chrome(Google Chrome)',3,0,0),('2023-06-01','Unknown(Unknown)',2,0,0),('2023-06-02','Unknown(Unknown)',2,0,0),('2023-06-07','Chrome(Google Chrome)',1,1,0),('2023-06-07','Unknown(Unknown)',2,0,0),('2023-06-09','Chrome(Google Chrome)',1,0,0),('2023-06-09','Unknown(Unknown)',2,0,0),('2023-06-13','Chrome(Google Chrome)',2,0,0),('2023-06-13','Unknown(Unknown)',2,0,0),('2023-06-14','Chrome(Google Chrome)',2,0,0),('2023-06-14','Unknown(Unknown)',2,0,0),('2023-06-15','Chrome(Google Chrome)',1,0,0),('2023-06-15','Unknown(Unknown)',2,0,0),('2023-06-16','Chrome(Google Chrome)',1,0,0),('2023-06-16','Unknown(Unknown)',2,0,0),('2023-06-21','Chrome(Google Chrome)',1,0,0),('2023-06-21','Unknown(Unknown)',2,0,0),('2023-06-22','Chrome(Google Chrome)',1,0,0),('2023-06-22','Unknown(Unknown)',2,0,0),('2023-06-27','Chrome(Google Chrome)',1,0,0),('2023-06-27','Unknown(Unknown)',2,0,0),('2023-08-01','Chrome(Google Chrome)',1,0,0),('2023-08-01','Unknown(Unknown)',2,0,0),('2023-08-02','Chrome(Google Chrome)',1,0,0),('2023-08-02','Unknown(Unknown)',2,0,0),('2023-08-04','Chrome(Google Chrome)',1,0,0),('2023-08-04','Unknown(Unknown)',2,0,0),('2023-08-07','Chrome(Google Chrome)',1,0,0),('2023-08-10','Chrome(Google Chrome)',1,0,0),('2023-08-23','Chrome(Google Chrome)',1,0,0),('2023-08-23','Unknown(Unknown)',2,0,0),('2023-09-04','Chrome(Google Chrome)',3,0,0),('2023-09-04','Unknown(Unknown)',2,0,0),('2023-09-05','Chrome(Google Chrome)',1,0,0),('2023-09-05','Unknown(Unknown)',2,0,0),('2023-09-06','Chrome(Google Chrome)',2,0,0),('2023-09-06','Unknown(Unknown)',2,0,0),('2023-09-07','Chrome(Google Chrome)',1,0,0),('2023-09-07','Unknown(Unknown)',2,0,0),('2023-09-13','Chrome(Google Chrome)',1,0,0),('2023-09-13','Unknown(Unknown)',2,0,0),('2023-09-14','Chrome(Google Chrome)',1,0,0),('2023-09-14','Unknown(Unknown)',2,0,0),('2023-09-19','Chrome(Google Chrome)',2,0,0),('2023-09-19','Unknown(Unknown)',2,0,0),('2023-10-31','Chrome(Google Chrome)',1,0,0),('2023-10-31','Unknown(Unknown)',2,0,0),('2023-11-02','Chrome(Google Chrome)',1,0,0),('2023-11-02','Unknown(Unknown)',2,0,0),('2023-11-07','Chrome(Google Chrome)',2,0,0),('2023-11-07','Unknown(Unknown)',2,0,0),('2023-11-15','Chrome(Google Chrome)',3,0,0),('2023-11-15','Unknown(Unknown)',2,0,0),('2023-11-20','Chrome(Google Chrome)',1,0,0),('2023-11-20','Unknown(Unknown)',2,0,0),('2023-11-23','Chrome(Google Chrome)',2,0,0),('2023-11-23','Unknown(Unknown)',2,0,0),('2023-11-24','Chrome(Google Chrome)',1,0,0),('2023-11-24','Unknown(Unknown)',2,0,0),('2023-11-29','Chrome(Google Chrome)',1,0,0),('2023-12-01','Chrome(Google Chrome)',1,0,0),('2023-12-01','Unknown(Unknown)',2,0,0),('2023-12-04','Chrome(Google Chrome)',1,0,0),('2023-12-04','Unknown(Unknown)',2,0,0),('2023-12-05','Chrome(Google Chrome)',1,0,0),('2023-12-05','Unknown(Unknown)',2,0,0),('2023-12-19','Chrome(Google Chrome)',1,0,0),('2023-12-20','Unknown(Unknown)',2,0,0),('2023-12-20','Chrome(Google Chrome)',1,0,0),('2023-12-21','Chrome(Google Chrome)',1,0,0),('2023-12-21','Unknown(Unknown)',2,0,0),('2024-01-22','Chrome(Google Chrome)',2,0,0),('2024-01-22','Unknown(Unknown)',2,0,0),('2024-01-25','Chrome(Google Chrome)',2,0,0),('2024-01-25','Unknown(Unknown)',2,0,0),('2024-01-29','Chrome(Google Chrome)',1,0,0),('2024-01-29','Unknown(Unknown)',2,0,0),('2024-01-31','Chrome(Google Chrome)',1,0,0),('2024-01-31','Unknown(Unknown)',2,0,0),('2024-02-08','Chrome(Google Chrome)',2,0,0),('2024-02-08','Unknown(Unknown)',2,0,0),('2024-02-13','Chrome(Google Chrome)',1,0,0),('2024-02-27','Chrome(Google Chrome)',2,0,0),('2024-02-27','Unknown(Unknown)',2,0,0);
/*!40000 ALTER TABLE `smart_cntlog_browser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_config`
--

DROP TABLE IF EXISTS `smart_cntlog_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_config` (
  `clc_uid` int(11) NOT NULL AUTO_INCREMENT,
  `clc_counter_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '카운터 사용여부',
  `clc_cookie_use` enum('A','T','O') NOT NULL DEFAULT 'A' COMMENT '중복 카운터 적용 여부 - A:접속하는대로 카운터 증가, T:지정된시간대로 카운터 증가, O:하루에 한번만 카운터 증가',
  `clc_cookie_term` int(11) NOT NULL DEFAULT 0 COMMENT '쿠키 지속 시간 - 초단위',
  `clc_total_num` int(11) NOT NULL DEFAULT 0 COMMENT '총 접속자 수',
  `clc_admin_check_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '관리자 접속 카운터 여부',
  `clc_admin_ip` char(30) NOT NULL COMMENT '관리자 아이피',
  PRIMARY KEY (`clc_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='카운터 설정';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_config`
--

LOCK TABLES `smart_cntlog_config` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_config` DISABLE KEYS */;
INSERT INTO `smart_cntlog_config` VALUES (1,'Y','A',0,168,'N','');
/*!40000 ALTER TABLE `smart_cntlog_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_detail`
--

DROP TABLE IF EXISTS `smart_cntlog_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_detail` (
  `sc_uid` int(11) NOT NULL COMMENT '고유값',
  `sc_url` varchar(255) NOT NULL COMMENT 'url',
  `sc_referer` varchar(255) NOT NULL COMMENT 'referer',
  `sc_keyword` varchar(255) NOT NULL COMMENT '키워드',
  `sc_ip` varchar(50) NOT NULL COMMENT '아이피',
  `sc_device` varchar(255) NOT NULL COMMENT '접속기기',
  `sc_os` varchar(255) NOT NULL COMMENT 'os',
  `sc_browser` varchar(255) NOT NULL COMMENT '브라우저',
  UNIQUE KEY `sc_uid` (`sc_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속자통계-상세';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_detail`
--

LOCK TABLES `smart_cntlog_detail` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_detail` DISABLE KEYS */;
INSERT INTO `smart_cntlog_detail` VALUES (1,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(2,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(3,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(4,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(5,'/','','','112.219.125.10','NateOn/7.0.16.0 (5451)','Unknown','NateOn(NateOn)'),(6,'/','','','112.219.125.10','NateOn/7.0.16.0 (5451)','Unknown','NateOn(NateOn)'),(7,'/','','','112.219.125.10','WebInfoUtil/1.0 (Linux; Android;) Mobile Safari','android','Safari(Apple Safari)'),(8,'/','','','112.219.125.10','Mozilla/5.0 (Linux; Android 12; SM-N971N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36','android','Chrome(Google Chrome)'),(9,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(10,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(11,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(12,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(13,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(14,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(15,'/?pn=board.list&_menu=event','','','112.219.125.10','Mozilla/5.0 (iPhone; CPU iPhone OS 16_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/262.1.529165518 Mobile/20D67 Safari/604.1','ios','Safari(Apple Safari)'),(16,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(17,'/?pn=board.list&_menu=event','','','112.219.125.10','Mozilla/5.0 (iPhone; CPU iPhone OS 16_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/262.1.529165518 Mobile/20D67 Safari/604.1','ios','Safari(Apple Safari)'),(18,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(19,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(20,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(21,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(22,'/totalAdmin/_config.display.timesale.php','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(23,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(24,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(25,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(26,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(27,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(28,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(29,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(30,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(31,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(32,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(33,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(34,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(35,'/','','','112.219.125.10','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Mobile Safari/537.36','android','Chrome(Google Chrome)'),(36,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(37,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(38,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(39,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(40,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(41,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(42,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(43,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(44,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(45,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(46,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(47,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(48,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(49,'/','http://hyplus01.onedaynet.co.kr/?pn=promotion.attend','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(50,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(51,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(52,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(53,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(54,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(55,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(56,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(57,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(58,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(59,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(60,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(61,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(62,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(63,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(64,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(65,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(66,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(67,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(68,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(69,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(70,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(71,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(72,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(73,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(74,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(75,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(76,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(77,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(78,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(79,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(80,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(81,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(82,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(83,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(84,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(85,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(86,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(87,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(88,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(89,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(90,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(91,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(92,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(93,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(94,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(95,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(96,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(97,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(98,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(99,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(100,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(101,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(102,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(103,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(104,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(105,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(106,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(107,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Whale/3.23.214.10 Safari/537.36','Windows10','Chrome(Google Chrome)'),(108,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(109,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(110,'/program/_db.google_login.php','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(111,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(112,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(113,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(114,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(115,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(116,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(117,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(118,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(119,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(120,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(121,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(122,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(123,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(124,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(125,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(126,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(127,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(128,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(129,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(130,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(131,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(132,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(133,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(134,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(135,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(136,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(137,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(138,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(139,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(140,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(141,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(142,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(143,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(144,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(145,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(146,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(147,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(148,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(149,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(150,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(151,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(152,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(153,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(154,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(155,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(156,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(157,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(158,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(159,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(160,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(161,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(162,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(163,'/totalAdmin/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(164,'/totalAdmin/_product_ticket.list.php?menuUid=262','http://hyplus01.onedaynet.co.kr/totalAdmin/_product.list.php?menuUid=77','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(165,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)'),(166,'/program/inc.member_groupset_auto.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(167,'/program/_1day.update.php','','','121.78.246.55','','Unknown','Unknown(Unknown)'),(168,'/','','','112.219.125.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36','Windows10','Chrome(Google Chrome)');
/*!40000 ALTER TABLE `smart_cntlog_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_device`
--

DROP TABLE IF EXISTS `smart_cntlog_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_device` (
  `scd_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '접속날짜',
  `scd_device` varchar(50) NOT NULL COMMENT '접속 디바이스',
  `scd_cnt` int(10) NOT NULL DEFAULT 0 COMMENT '누적수',
  UNIQUE KEY `scd_unique` (`scd_date`,`scd_device`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속자 - Device 통계';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_device`
--

LOCK TABLES `smart_cntlog_device` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_device` DISABLE KEYS */;
INSERT INTO `smart_cntlog_device` VALUES ('2023-05-11','PC',6),('2023-05-11','MOBILE',2),('2023-05-15','PC',3),('2023-05-16','PC',3),('2023-05-17','MOBILE',2),('2023-05-17','PC',2),('2023-05-18','PC',3),('2023-05-24','PC',3),('2023-05-26','PC',3),('2023-06-01','PC',5),('2023-06-02','PC',2),('2023-06-07','MOBILE',1),('2023-06-07','PC',3),('2023-06-09','PC',3),('2023-06-13','PC',4),('2023-06-14','PC',4),('2023-06-15','PC',3),('2023-06-16','PC',3),('2023-06-21','PC',3),('2023-06-22','PC',3),('2023-06-27','PC',3),('2023-08-01','PC',3),('2023-08-02','PC',3),('2023-08-04','PC',3),('2023-08-07','PC',1),('2023-08-10','PC',1),('2023-08-23','PC',3),('2023-09-04','PC',5),('2023-09-05','PC',3),('2023-09-06','PC',4),('2023-09-07','PC',3),('2023-09-13','PC',3),('2023-09-14','PC',3),('2023-09-19','PC',4),('2023-10-31','PC',3),('2023-11-02','PC',3),('2023-11-07','PC',4),('2023-11-15','PC',5),('2023-11-20','PC',3),('2023-11-23','PC',4),('2023-11-24','PC',3),('2023-11-29','PC',1),('2023-12-01','PC',3),('2023-12-04','PC',3),('2023-12-05','PC',3),('2023-12-19','PC',1),('2023-12-20','PC',3),('2023-12-21','PC',3),('2024-01-22','PC',4),('2024-01-25','PC',4),('2024-01-29','PC',3),('2024-01-31','PC',3),('2024-02-08','PC',4),('2024-02-13','PC',1),('2024-02-27','PC',4);
/*!40000 ALTER TABLE `smart_cntlog_device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_ip`
--

DROP TABLE IF EXISTS `smart_cntlog_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_ip` (
  `sci_rdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '접속일시',
  `sci_ip` char(30) NOT NULL COMMENT '접속 아이피 - smart_cntlog_Config - clc_cookie_use가 A:접속하는대로 카운터 증가일 경우 사용하지 않음',
  UNIQUE KEY `sck_unique` (`sci_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속 - IP 기록 - 카운터증가_로봇검출 기능 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_ip`
--

LOCK TABLES `smart_cntlog_ip` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_cntlog_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_keyword`
--

DROP TABLE IF EXISTS `smart_cntlog_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_keyword` (
  `sck_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '접속날짜',
  `sck_keyword` varchar(50) NOT NULL COMMENT '접속 키워드',
  `sck_cnt_pc` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - PC',
  `sck_cnt_mo` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 모바일',
  `sck_cnt_app` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 앱',
  UNIQUE KEY `sck_unique` (`sck_date`,`sck_keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속 - 키워드 통계';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_keyword`
--

LOCK TABLES `smart_cntlog_keyword` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_keyword` DISABLE KEYS */;
INSERT INTO `smart_cntlog_keyword` VALUES ('2023-05-11','',6,2,0),('2023-05-15','',3,0,0),('2023-05-16','',3,0,0),('2023-05-17','',2,2,0),('2023-05-18','',3,0,0),('2023-05-24','',3,0,0),('2023-05-26','',3,0,0),('2023-06-01','',5,0,0),('2023-06-02','',2,0,0),('2023-06-07','',3,1,0),('2023-06-09','',3,0,0),('2023-06-13','',4,0,0),('2023-06-14','',4,0,0),('2023-06-15','',3,0,0),('2023-06-16','',3,0,0),('2023-06-21','',3,0,0),('2023-06-22','',3,0,0),('2023-06-27','',3,0,0),('2023-08-01','',3,0,0),('2023-08-02','',3,0,0),('2023-08-04','',3,0,0),('2023-08-07','',1,0,0),('2023-08-10','',1,0,0),('2023-08-23','',3,0,0),('2023-09-04','',5,0,0),('2023-09-05','',3,0,0),('2023-09-06','',4,0,0),('2023-09-07','',3,0,0),('2023-09-13','',3,0,0),('2023-09-14','',3,0,0),('2023-09-19','',4,0,0),('2023-10-31','',3,0,0),('2023-11-02','',3,0,0),('2023-11-07','',4,0,0),('2023-11-15','',5,0,0),('2023-11-20','',3,0,0),('2023-11-23','',4,0,0),('2023-11-24','',3,0,0),('2023-11-29','',1,0,0),('2023-12-01','',3,0,0),('2023-12-04','',3,0,0),('2023-12-05','',3,0,0),('2023-12-19','',1,0,0),('2023-12-20','',3,0,0),('2023-12-21','',3,0,0),('2024-01-22','',4,0,0),('2024-01-25','',4,0,0),('2024-01-29','',3,0,0),('2024-01-31','',3,0,0),('2024-02-08','',4,0,0),('2024-02-13','',1,0,0),('2024-02-27','',4,0,0);
/*!40000 ALTER TABLE `smart_cntlog_keyword` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_list`
--

DROP TABLE IF EXISTS `smart_cntlog_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_list` (
  `sc_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유값',
  `sc_mobile` enum('Y','N','A') NOT NULL DEFAULT 'N' COMMENT '모바일여부 - Y:모바일, N:PC , A:app (앱)',
  `sc_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '접속날짜',
  `sc_memtype` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '회원여부',
  `sc_mid` varchar(50) DEFAULT NULL COMMENT '회원아이디 - 아이디 없을 경우 NULL',
  `sc_cookie` varchar(50) NOT NULL COMMENT '쿠키 아이디 - 구분자',
  PRIMARY KEY (`sc_uid`),
  KEY `sc_mobile` (`sc_mobile`),
  KEY `sc_memtype` (`sc_memtype`)
) ENGINE=MyISAM AUTO_INCREMENT=169 DEFAULT CHARSET=utf8 COMMENT='접속자통계-기본';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_list`
--

LOCK TABLES `smart_cntlog_list` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_list` DISABLE KEYS */;
INSERT INTO `smart_cntlog_list` VALUES (1,'N','2023-05-11 11:52:50','N','','6882b216132d1aee25e5d3f93b9f559f'),(2,'N','2023-05-11 11:52:50','N','','e5eda985bdcba98b4fafa30a7f51c5fa'),(3,'N','2023-05-11 11:52:50','N','','c8715f44901f0d5f312470fb56c59ff5'),(4,'N','2023-05-11 11:53:48','N','','135db116186862bdc9f77cf588618242'),(5,'N','2023-05-11 11:59:39','N','','0f2d70275901dfea45d39524ba904ea7'),(6,'N','2023-05-11 11:59:39','N','','7976899098402c74409c7c4f8fde3afd'),(7,'Y','2023-05-11 11:59:56','N','','74cf7df8ee1519ddb4f6419e80c3dcca'),(8,'Y','2023-05-11 11:59:58','N','','e1656e2556bd6ea2706bb389e975a1f5'),(9,'N','2023-05-15 15:32:55','N','','39491d7ff33d996cd34b77eef55b4ab2'),(10,'N','2023-05-15 15:32:55','N','','011e99e09aabe3e9929fa1ba19b4a979'),(11,'N','2023-05-15 15:32:55','N','','28d6e1cbf6f3939ccf5c1a52777bc393'),(12,'N','2023-05-16 08:59:25','N','','20fe28d6c9ec6b9aadb636bf212196e3'),(13,'N','2023-05-16 08:59:25','N','','3412daeb4b05a440dec2006f3112e5ab'),(14,'N','2023-05-16 08:59:25','N','','67fc2486a33481a5196c96895e2d252c'),(15,'Y','2023-05-17 14:33:27','N','','cc1851b418af837283aec78fecce585c'),(16,'N','2023-05-17 14:33:27','N','','e841481db01c0bd858641d611ad2eb85'),(17,'Y','2023-05-17 14:33:27','N','','c90a3cfe5b7e8e356caec3c5adcc5855'),(18,'N','2023-05-17 14:33:28','N','','f83c4ef442b72e1c82bcd9f9a1870409'),(19,'N','2023-05-18 10:07:24','N','','b16cc34237d3858d848e693a12195788'),(20,'N','2023-05-18 10:07:24','N','','cdea02825ab80989a2e8ab3761cbc891'),(21,'N','2023-05-18 10:07:24','N','','f6df7249f6d412f931acf9ac5cbc1921'),(22,'N','2023-05-24 11:23:40','N','','b3a2b4f0c2078ad30d38c78ec6d892a9'),(23,'N','2023-05-24 14:14:34','N','','102f1c6ff3ebbf0fcefe8ee195afa845'),(24,'N','2023-05-24 14:14:34','N','','bf4e378db347cad21e1442159d00ccb1'),(25,'N','2023-05-26 17:53:10','N','','4a4a7c0d9adda37523b710ca24214457'),(26,'N','2023-05-26 17:53:10','N','','7244e454dd4b0bccddf078ce9523a2f1'),(27,'N','2023-05-26 17:53:10','N','','bdd27ab01d643779dc4f1d3204bd1e8c'),(28,'N','2023-06-01 11:05:02','N','','f08ee244484cdb6a628880fff219c890'),(29,'N','2023-06-01 11:05:02','N','','5e1ff8cdd61865f4caa13315058f0319'),(30,'N','2023-06-01 11:05:02','N','','e6669ee3cfadefbcca9d4763fe089457'),(31,'N','2023-06-01 11:05:23','N','','02e8950b136ec824b565d06b751ca36f'),(32,'N','2023-06-01 13:17:40','N','','8212e401ccc7234c93d96774e7a77810'),(33,'N','2023-06-02 11:17:46','N','','77e6def7bbac92b9f01950cc286367e3'),(34,'N','2023-06-02 11:17:46','N','','1dc426b456e3d37fb031d4db7af5a366'),(35,'Y','2023-06-07 18:05:11','N','','c175e72ccfef56d6c74ad342b45495aa'),(36,'N','2023-06-07 18:05:11','N','','ddd5557181c46f826c61cc25b8c820c6'),(37,'N','2023-06-07 18:05:11','N','','37fae524d33739de33653e207e231322'),(38,'N','2023-06-07 18:09:54','N','','43dc5c5954440141341045ede7eb66de'),(39,'N','2023-06-09 14:05:27','Y','master','c15f4a7a4f6628b19580ba449f73a5cd'),(40,'N','2023-06-09 14:05:27','N','','e7e7cd558316e9c2a13f41f9642a4bb9'),(41,'N','2023-06-09 14:05:27','N','','b3c46e968b8b904d245d58eb443c2fcf'),(42,'N','2023-06-13 11:58:57','N','','7a9839373b4fa126271b0336638600c5'),(43,'N','2023-06-13 11:58:57','N','','41b57c12c6cfed34633fc8233de34c43'),(44,'N','2023-06-13 11:58:57','N','','f997a22f214c200df96faea24eac604a'),(45,'N','2023-06-13 13:42:52','N','','b642efbb8771ba43c6dae9dd4f126234'),(46,'N','2023-06-14 09:16:04','N','','160bab5714b764479e9e8b2005482b8e'),(47,'N','2023-06-14 09:16:04','N','','7bb196ab4effb198ecb8cb5ca28a11ee'),(48,'N','2023-06-14 09:16:04','N','','14ea1a94e1a8924a6c559cfa4cced729'),(49,'N','2023-06-14 10:18:00','N','','12ed0e00af9d5bf62ce7e86847976d10'),(50,'N','2023-06-15 11:49:42','N','','7e9553f9ae7b27b367bbc185f10b5de7'),(51,'N','2023-06-15 11:49:42','N','','904f2cd1ef4cb7e218716865e7ee94ac'),(52,'N','2023-06-15 11:49:42','N','','18c98200745c6e4805e09ea325d392c2'),(53,'N','2023-06-16 17:46:34','N','','a1cba5fd810a63d42b6575c47475f715'),(54,'N','2023-06-16 17:46:38','N','','a137abcc75c3e9abe65a2e0012b8a8b1'),(55,'N','2023-06-16 17:46:38','N','','4b948f1fa96bd80db37748df7d256163'),(56,'N','2023-06-21 16:35:17','N','','21cb1e242e1a68d77fc27e332952d508'),(57,'N','2023-06-21 16:35:17','N','','430730b4058a2fbacd2263eaaa477136'),(58,'N','2023-06-21 16:35:17','N','','1067711279f987097262ee4460bce44f'),(59,'N','2023-06-22 11:08:31','N','','4a29413db61d285d0cb18dc2ebd0b978'),(60,'N','2023-06-22 11:08:31','N','','4165254ef4a6887a581d9f9971536724'),(61,'N','2023-06-22 11:08:31','N','','686142bd90bf96c5edca96ccb427b551'),(62,'N','2023-06-27 14:41:12','N','','7bf39c8388429f19c460665e1e782f1e'),(63,'N','2023-06-27 14:41:12','N','','89894be8c4f8168634584b9b489fccca'),(64,'N','2023-06-27 14:41:12','N','','659fbd109b99124f4c4cff69165da0e5'),(65,'N','2023-08-01 10:24:06','N','','b20c90a6d81f1477f5457aa1dcaebff8'),(66,'N','2023-08-01 10:24:07','N','','ef34ea03a164cdcb6ff247cc44cc9b55'),(67,'N','2023-08-01 10:24:07','N','','56c1039ab92c8a1a27ecc345ce42852a'),(68,'N','2023-08-02 13:45:18','N','','d097ee5a7384913a0ecd75e6930f692b'),(69,'N','2023-08-02 13:45:18','N','','7ab7395dee7c6c2eb34f9a9466ad71a2'),(70,'N','2023-08-02 13:45:19','N','','143246391315836a944c929692c1c365'),(71,'N','2023-08-04 17:36:25','N','','3510a359acee25f29069ad27b259a058'),(72,'N','2023-08-04 17:36:25','N','','99dd42103626b51b00b659d9e8e00f07'),(73,'N','2023-08-04 17:36:25','N','','b671fbcf60d41a42a7c3e0f5bdf32c20'),(74,'N','2023-08-07 11:11:00','N','','751399b621386adbbf9486064f0387a7'),(75,'N','2023-08-10 16:22:01','N','','84cd9746e8ce04da2a4ef75cd33bb10d'),(76,'N','2023-08-23 15:34:59','N','','8caa55598b3dddab3aea01e7380d410f'),(77,'N','2023-08-23 15:34:59','N','','30a90fd931143f96661f27607c1941d8'),(78,'N','2023-08-23 15:34:59','N','','0dbc85f28b6a30d8ec4acf1b6f253138'),(79,'N','2023-09-04 18:19:22','N','','a05b973fe90adfce1231e07e34ca096c'),(80,'N','2023-09-04 18:19:22','N','','beecf5713e917ee1c19a9dfaa0d55a3c'),(81,'N','2023-09-04 18:19:22','N','','6ae4135d06fc47f4a4d2b47f3459efa2'),(82,'N','2023-09-04 18:19:29','N','','6802949e92e2b9b98440dca2a3e93978'),(83,'N','2023-09-04 18:20:34','N','','42f7230ae68226a683a7abe075397c53'),(84,'N','2023-09-05 10:53:41','N','','6fb507e2e6611412e89ea81ed9092a1f'),(85,'N','2023-09-05 10:53:41','N','','06e1a60db41359438e95cd4c944f3966'),(86,'N','2023-09-05 10:53:41','N','','2eabd26a6451c2a4a7856a036047c53d'),(87,'N','2023-09-06 11:28:01','N','','128e8c03f68238d34f9936b9a775a74e'),(88,'N','2023-09-06 11:28:01','N','','98f674da182ff1aa16b8c704ad164f6d'),(89,'N','2023-09-06 11:28:01','N','','42f1231d71ae135fe2b7fdc6041a6a2d'),(90,'N','2023-09-06 11:31:52','N','','59797df07b4bfa2d3fda7ae6b1b43417'),(91,'N','2023-09-07 09:18:07','N','','3aa4cc5f75d8bf1220c1c6a1a283775c'),(92,'N','2023-09-07 09:18:07','N','','a9802ff2f083b3043ace1df2e433e442'),(93,'N','2023-09-07 09:18:07','N','','b247c86f66500c0f0cc0e1a003ae7cae'),(94,'N','2023-09-13 15:34:30','N','','3fbb6ec07b12e1fc253e063044ec84c2'),(95,'N','2023-09-13 15:34:31','N','','c1154d81ca52f6cce838baf74691a161'),(96,'N','2023-09-13 15:34:31','N','','75420632e6fcea3041ebfe44abd2968e'),(97,'N','2023-09-14 16:08:05','Y','master','a1706717f810702996253244865d4d30'),(98,'N','2023-09-14 16:08:05','N','','ec7de6901bb841de40e47e7ff5120207'),(99,'N','2023-09-14 16:08:05','N','','999440b2dcded64cc1764ad3f01359aa'),(100,'N','2023-09-19 09:53:41','N','','a47ade9e8ae9e7b5da544581f3ce256e'),(101,'N','2023-09-19 09:53:41','N','','2907f8634b6ec8acb46e26c33bc020cd'),(102,'N','2023-09-19 09:53:41','N','','20fbb5144b4879d4231634bdec1b2a62'),(103,'N','2023-09-19 10:23:12','Y','master','877355754fd64d7b9bf781cd96e21791'),(104,'N','2023-10-31 17:58:12','N','','65b8549f64a0f0f9734bb23c4a95a7d2'),(105,'N','2023-10-31 17:58:12','N','','40d016715a660a9fb5781aa60c28a41b'),(106,'N','2023-10-31 17:58:12','N','','3a5eca9e17ded64d02e1d255e770eacf'),(107,'N','2023-11-02 11:46:13','N','','de5248dacb4749a29be5f71a130c49ca'),(108,'N','2023-11-02 11:46:13','N','','2551b263cfe4b9316c11d15bab1dcaee'),(109,'N','2023-11-02 11:46:13','N','','44344e4338263e650f6d439e59a8349a'),(110,'N','2023-11-07 12:48:47','N','','c9574839b6aa2bea16b199c733edc74c'),(111,'N','2023-11-07 14:10:31','N','','07a39cd4b9e59ac56d5e88657cf5ea38'),(112,'N','2023-11-07 14:10:32','N','','03b40f83212adf11287bcdf0dd877125'),(113,'N','2023-11-07 14:10:32','N','','12198c691e9ebcd500117fa28c23bef2'),(114,'N','2023-11-15 17:00:55','N','','770ea1f728252b07577eb168dabce69e'),(115,'N','2023-11-15 17:00:56','N','','abfe19f07f1370cc1ce8a10c554ed1b4'),(116,'N','2023-11-15 17:00:56','N','','cf1f253135c2cfab17accada12da273b'),(117,'N','2023-11-15 17:10:07','N','','45d2f2198fc3e4d54e6416e3a75b49dd'),(118,'N','2023-11-15 17:10:51','N','','4b8cd4c18d281afe3ee4ae7949a80c92'),(119,'N','2023-11-20 14:49:53','N','','f3fded40dabaf740615fdc48c89838af'),(120,'N','2023-11-20 14:49:53','N','','9b60dd9daa518540cf691e8fe1c81af6'),(121,'N','2023-11-20 14:49:53','N','','ff273eed4955f7fcf9e4c49982c65f8e'),(122,'N','2023-11-23 09:45:05','N','','d4db5d3af6a5882d5dc7e1d6ada2e997'),(123,'N','2023-11-23 09:45:05','N','','36334c9448d6416dc3a27260e953a769'),(124,'N','2023-11-23 09:45:05','N','','8fc92b0eeebc150af8bbfa91b9c63e8a'),(125,'N','2023-11-23 13:56:21','N','','0b5b63d43f82323485a309ce9033c561'),(126,'N','2023-11-24 15:53:39','N','','fe744b623270cdd9613d8d61b4a50a93'),(127,'N','2023-11-24 15:53:39','N','','ce426254c99b029bd851da3abb37d6d4'),(128,'N','2023-11-24 15:53:39','N','','5c270a064272a0b09fd1eb1abcd236ea'),(129,'N','2023-11-29 18:00:12','N','','b3d6b1d8b2313b0de3ffbdf97e61c9c2'),(130,'N','2023-12-01 17:39:53','N','','9b20d92224bf38d06f48fe51109bafa0'),(131,'N','2023-12-01 17:39:54','N','','4569c95775362233c878a2bf56788937'),(132,'N','2023-12-01 17:39:54','N','','56fcea1eaf3997e2822d2b809fe7a07d'),(133,'N','2023-12-04 18:09:51','N','','d41a150c961bb78dd3d9f5062da27d06'),(134,'N','2023-12-04 18:09:51','N','','060a118daeb8fd7c4b5e206a7b3ee703'),(135,'N','2023-12-04 18:09:51','N','','490d5d5a4f77bdcebe8db17bee29c664'),(136,'N','2023-12-05 10:11:53','N','','ca7722b60669b4bcbe9781f6f965f761'),(137,'N','2023-12-05 10:11:53','N','','837d8a18ebf23f158e8474e1bbfb5ad4'),(138,'N','2023-12-05 10:11:53','N','','9e2946bab5aa6cfd0121f1cb2b72b10a'),(139,'N','2023-12-19 09:32:38','N','','eb5000a4f98c36488d85c23fbf428551'),(140,'N','2023-12-20 10:27:41','N','','20c20d7e807a2a1f27c31386ca62bcfe'),(141,'N','2023-12-20 10:27:41','N','','879b684a429fd95abaf9809c3eee7c4e'),(142,'N','2023-12-20 18:34:47','N','','516611ba6813617a6f7d62ebe71d8258'),(143,'N','2023-12-21 13:46:59','N','','08947fe87990bf29924d96dfb0ca0978'),(144,'N','2023-12-21 13:46:59','N','','e1f04d2165bf4faa712d6741c6124078'),(145,'N','2023-12-21 13:46:59','N','','19f680cf169efb5afec8a3fa9fabf282'),(146,'N','2024-01-22 09:53:05','N','','0cc5831b7edcfc6b5a4761e95f3c4d52'),(147,'N','2024-01-22 14:08:27','N','','634d47640067f8bd4c3293ab326c7e4e'),(148,'N','2024-01-22 14:08:27','N','','6d31050c6ebcfa1cfb17510735f526fa'),(149,'N','2024-01-22 14:08:27','N','','9b72a9d3fac458f2603f1a1c7e24c943'),(150,'N','2024-01-25 10:30:33','N','','d38b2affad421b4bee1cc2fde4bba4fb'),(151,'N','2024-01-25 10:30:33','N','','7c4d96c1fd3894559525b78bdcf34911'),(152,'N','2024-01-25 10:30:33','N','','09c51d32179505b8c2c0f16d23319b73'),(153,'N','2024-01-25 14:03:50','N','','f5c61a6ca3d420505e8ebb1fc01a32c6'),(154,'N','2024-01-29 11:59:06','N','','41b66a0f9e44a997f92e34a779817670'),(155,'N','2024-01-29 11:59:06','N','','6cb8d6998a1b09d2e42456559b97fc0c'),(156,'N','2024-01-29 11:59:06','N','','ae7f406e4a5cb00936fea17c7dc5d8fb'),(157,'N','2024-01-31 11:05:24','N','','70b31a4363a2a2c82a601af2ccd6c90e'),(158,'N','2024-01-31 11:05:24','N','','ab51f901840dc7e3c7c075f370782d92'),(159,'N','2024-01-31 11:05:24','N','','e22722d467d59c4b8e1078441deb07da'),(160,'N','2024-02-08 10:47:33','N','','4ee5bff407bdc6efe0fb8dfd9601bd8a'),(161,'N','2024-02-08 10:47:33','N','','fa3f6ff15b8fa2d16c2545d481b82be5'),(162,'N','2024-02-08 10:47:33','N','','8f616c366799e5bd662f93c8fdfe7fe9'),(163,'N','2024-02-08 10:54:29','N','','6475090a4677610a80315465d562bdfa'),(164,'N','2024-02-13 15:50:50','N','','9f2e73d63503775b17fd82b89a87c4f2'),(165,'N','2024-02-27 09:53:54','N','','e43246cc7b2f5a05bbc92c7dc356e8f2'),(166,'N','2024-02-27 09:53:54','N','','0eb64262794f63a5145830caa48a3217'),(167,'N','2024-02-27 09:53:54','N','','038fa88f80fe3c01b4b5116c6c712b68'),(168,'N','2024-02-27 10:20:57','N','','24d354181b2a64a01edb79e3fbfc1868');
/*!40000 ALTER TABLE `smart_cntlog_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_os`
--

DROP TABLE IF EXISTS `smart_cntlog_os`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_os` (
  `sco_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '접속날짜',
  `sco_os` varchar(50) NOT NULL COMMENT '접속 OS',
  `sco_cnt_pc` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - PC',
  `sco_cnt_mo` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 모바일',
  `sco_cnt_app` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 앱',
  UNIQUE KEY `sco_unique` (`sco_date`,`sco_os`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속 - OS 통계';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_os`
--

LOCK TABLES `smart_cntlog_os` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_os` DISABLE KEYS */;
INSERT INTO `smart_cntlog_os` VALUES ('2023-05-11','Windows10',2,0,0),('2023-05-11','Unknown',4,0,0),('2023-05-11','android',0,2,0),('2023-05-15','Windows10',1,0,0),('2023-05-15','Unknown',2,0,0),('2023-05-16','Windows10',1,0,0),('2023-05-16','Unknown',2,0,0),('2023-05-17','ios',0,2,0),('2023-05-17','Unknown',2,0,0),('2023-05-18','Windows10',1,0,0),('2023-05-18','Unknown',2,0,0),('2023-05-24','Windows10',1,0,0),('2023-05-24','Unknown',2,0,0),('2023-05-26','Windows10',1,0,0),('2023-05-26','Unknown',2,0,0),('2023-06-01','Windows10',3,0,0),('2023-06-01','Unknown',2,0,0),('2023-06-02','Unknown',2,0,0),('2023-06-07','android',0,1,0),('2023-06-07','Unknown',2,0,0),('2023-06-07','Windows10',1,0,0),('2023-06-09','Windows10',1,0,0),('2023-06-09','Unknown',2,0,0),('2023-06-13','Windows10',2,0,0),('2023-06-13','Unknown',2,0,0),('2023-06-14','Windows10',2,0,0),('2023-06-14','Unknown',2,0,0),('2023-06-15','Windows10',1,0,0),('2023-06-15','Unknown',2,0,0),('2023-06-16','Windows10',1,0,0),('2023-06-16','Unknown',2,0,0),('2023-06-21','Windows10',1,0,0),('2023-06-21','Unknown',2,0,0),('2023-06-22','Windows10',1,0,0),('2023-06-22','Unknown',2,0,0),('2023-06-27','Windows10',1,0,0),('2023-06-27','Unknown',2,0,0),('2023-08-01','Windows10',1,0,0),('2023-08-01','Unknown',2,0,0),('2023-08-02','Windows10',1,0,0),('2023-08-02','Unknown',2,0,0),('2023-08-04','Windows10',1,0,0),('2023-08-04','Unknown',2,0,0),('2023-08-07','Windows10',1,0,0),('2023-08-10','Windows10',1,0,0),('2023-08-23','Windows10',1,0,0),('2023-08-23','Unknown',2,0,0),('2023-09-04','Windows10',3,0,0),('2023-09-04','Unknown',2,0,0),('2023-09-05','Windows10',1,0,0),('2023-09-05','Unknown',2,0,0),('2023-09-06','Windows10',2,0,0),('2023-09-06','Unknown',2,0,0),('2023-09-07','Windows10',1,0,0),('2023-09-07','Unknown',2,0,0),('2023-09-13','Windows10',1,0,0),('2023-09-13','Unknown',2,0,0),('2023-09-14','Windows10',1,0,0),('2023-09-14','Unknown',2,0,0),('2023-09-19','Windows10',2,0,0),('2023-09-19','Unknown',2,0,0),('2023-10-31','Windows10',1,0,0),('2023-10-31','Unknown',2,0,0),('2023-11-02','Windows10',1,0,0),('2023-11-02','Unknown',2,0,0),('2023-11-07','Windows10',2,0,0),('2023-11-07','Unknown',2,0,0),('2023-11-15','Windows10',3,0,0),('2023-11-15','Unknown',2,0,0),('2023-11-20','Windows10',1,0,0),('2023-11-20','Unknown',2,0,0),('2023-11-23','Windows10',2,0,0),('2023-11-23','Unknown',2,0,0),('2023-11-24','Windows10',1,0,0),('2023-11-24','Unknown',2,0,0),('2023-11-29','Windows10',1,0,0),('2023-12-01','Windows10',1,0,0),('2023-12-01','Unknown',2,0,0),('2023-12-04','Windows10',1,0,0),('2023-12-04','Unknown',2,0,0),('2023-12-05','Windows10',1,0,0),('2023-12-05','Unknown',2,0,0),('2023-12-19','Windows10',1,0,0),('2023-12-20','Unknown',2,0,0),('2023-12-20','Windows10',1,0,0),('2023-12-21','Windows10',1,0,0),('2023-12-21','Unknown',2,0,0),('2024-01-22','Windows10',2,0,0),('2024-01-22','Unknown',2,0,0),('2024-01-25','Windows10',2,0,0),('2024-01-25','Unknown',2,0,0),('2024-01-29','Windows10',1,0,0),('2024-01-29','Unknown',2,0,0),('2024-01-31','Windows10',1,0,0),('2024-01-31','Unknown',2,0,0),('2024-02-08','Windows10',2,0,0),('2024-02-08','Unknown',2,0,0),('2024-02-13','Windows10',1,0,0),('2024-02-27','Windows10',2,0,0),('2024-02-27','Unknown',2,0,0);
/*!40000 ALTER TABLE `smart_cntlog_os` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_route`
--

DROP TABLE IF EXISTS `smart_cntlog_route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_route` (
  `scr_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '접속날짜',
  `scr_route` varchar(50) NOT NULL COMMENT '접속 경로 - 도메인단위',
  `scr_cnt_pc` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - PC',
  `scr_cnt_mo` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 모바일',
  `scr_cnt_app` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 앱',
  UNIQUE KEY `scr_unique` (`scr_date`,`scr_route`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속 - 경로 통계';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_route`
--

LOCK TABLES `smart_cntlog_route` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_route` DISABLE KEYS */;
INSERT INTO `smart_cntlog_route` VALUES ('2023-05-11','',6,2,0),('2023-05-15','',3,0,0),('2023-05-16','',3,0,0),('2023-05-17','',2,2,0),('2023-05-18','',3,0,0),('2023-05-24','',3,0,0),('2023-05-26','',3,0,0),('2023-06-01','',5,0,0),('2023-06-02','',2,0,0),('2023-06-07','',3,1,0),('2023-06-09','',3,0,0),('2023-06-13','',4,0,0),('2023-06-14','',4,0,0),('2023-06-15','',3,0,0),('2023-06-16','',3,0,0),('2023-06-21','',3,0,0),('2023-06-22','',3,0,0),('2023-06-27','',3,0,0),('2023-08-01','',3,0,0),('2023-08-02','',3,0,0),('2023-08-04','',3,0,0),('2023-08-07','',1,0,0),('2023-08-10','',1,0,0),('2023-08-23','',3,0,0),('2023-09-04','',5,0,0),('2023-09-05','',3,0,0),('2023-09-06','',4,0,0),('2023-09-07','',3,0,0),('2023-09-13','',3,0,0),('2023-09-14','',3,0,0),('2023-09-19','',4,0,0),('2023-10-31','',3,0,0),('2023-11-02','',3,0,0),('2023-11-07','',4,0,0),('2023-11-15','',5,0,0),('2023-11-20','',3,0,0),('2023-11-23','',4,0,0),('2023-11-24','',3,0,0),('2023-11-29','',1,0,0),('2023-12-01','',3,0,0),('2023-12-04','',3,0,0),('2023-12-05','',3,0,0),('2023-12-19','',1,0,0),('2023-12-20','',3,0,0),('2023-12-21','',3,0,0),('2024-01-22','',4,0,0),('2024-01-25','',4,0,0),('2024-01-29','',3,0,0),('2024-01-31','',3,0,0),('2024-02-08','',4,0,0),('2024-02-13','',1,0,0),('2024-02-27','',4,0,0);
/*!40000 ALTER TABLE `smart_cntlog_route` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_cntlog_sex`
--

DROP TABLE IF EXISTS `smart_cntlog_sex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_cntlog_sex` (
  `scs_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '접속날짜',
  `scs_sex` enum('M','F','N') NOT NULL COMMENT '접속 회원성 - M:남성, F:여성',
  `scs_cnt_pc` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - PC',
  `scs_cnt_mo` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 모바일',
  `scs_cnt_app` int(10) NOT NULL DEFAULT 0 COMMENT '누적수 - 앱',
  UNIQUE KEY `sck_unique` (`scs_date`,`scs_sex`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속 - 회원성별 통계';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_cntlog_sex`
--

LOCK TABLES `smart_cntlog_sex` WRITE;
/*!40000 ALTER TABLE `smart_cntlog_sex` DISABLE KEYS */;
INSERT INTO `smart_cntlog_sex` VALUES ('2023-06-09','F',1,0,0),('2023-09-14','F',1,0,0),('2023-09-19','F',1,0,0);
/*!40000 ALTER TABLE `smart_cntlog_sex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_common_option`
--

DROP TABLE IF EXISTS `smart_common_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_common_option` (
  `co_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '자주쓰는옵션 고유번호',
  `co_suid` int(11) NOT NULL COMMENT '자주쓰는옵션 설정 고유번호아이디',
  `co_poptionname` varchar(200) NOT NULL COMMENT '옵션명',
  `co_cnt` int(11) NOT NULL COMMENT '수량',
  `co_poptionprice` int(10) NOT NULL COMMENT '가격',
  `co_poption_supplyprice` int(10) NOT NULL COMMENT '공급가격',
  `co_depth` tinyint(2) NOT NULL COMMENT '차수',
  `co_parent` varchar(50) DEFAULT NULL COMMENT '부모',
  `co_view` enum('Y','N') NOT NULL COMMENT '노출',
  `co_sort` int(11) NOT NULL COMMENT '순서',
  `co_rdate` datetime NOT NULL COMMENT '등록일',
  `co_color_type` enum('color','img') DEFAULT NULL COMMENT 'color타입 - color:색상, img:이미지',
  `co_color_name` varchar(50) DEFAULT NULL COMMENT 'co_color_type에 따른 지정값 - color일 경우 colorpicker , img일 경우 파일명',
  PRIMARY KEY (`co_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='자주쓰는 옵션 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_common_option`
--

LOCK TABLES `smart_common_option` WRITE;
/*!40000 ALTER TABLE `smart_common_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_common_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_common_option_set`
--

DROP TABLE IF EXISTS `smart_common_option_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_common_option_set` (
  `cos_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '자주쓰는옵션 고유번호',
  `cos_name` varchar(255) NOT NULL COMMENT '자주쓰는옵션 명',
  `cos_type` enum('option','addoption') NOT NULL COMMENT '자주쓰는옵션 타입(옵션,추가옵션)',
  `cos_depth` tinyint(2) NOT NULL COMMENT '자주쓰는옵션 차수',
  `cos_rdate` datetime NOT NULL COMMENT '자주쓰는옵션 등록일',
  `cos_option1_type` enum('normal','size','color') NOT NULL DEFAULT 'normal' COMMENT '1차옵션타입 - normal:일반형, size:사이즈형, color:색상형',
  `cos_option2_type` enum('normal','size','color') NOT NULL DEFAULT 'normal' COMMENT '2차옵션타입 - normal:일반형, size:사이즈형, color:색상형',
  `cos_option3_type` enum('normal','size','color') NOT NULL DEFAULT 'normal' COMMENT '3차옵션타입 - normal:일반형, size:사이즈형, color:색상형',
  PRIMARY KEY (`cos_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='자주 쓰는 옵션 설정 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_common_option_set`
--

LOCK TABLES `smart_common_option_set` WRITE;
/*!40000 ALTER TABLE `smart_common_option_set` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_common_option_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_company`
--

DROP TABLE IF EXISTS `smart_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_company` (
  `cp_id` varchar(50) NOT NULL DEFAULT '',
  `cp_pw` varchar(100) NOT NULL DEFAULT '' COMMENT '패스워드',
  `cp_name` varchar(100) NOT NULL DEFAULT '' COMMENT '상점명',
  `cp_number` varchar(20) NOT NULL DEFAULT '' COMMENT '사업자번호',
  `cp_snumber` varchar(50) NOT NULL COMMENT '입점업체 통신판매업번호',
  `cp_ceoname` varchar(20) NOT NULL DEFAULT '' COMMENT '대표자명',
  `cp_address` varchar(100) NOT NULL DEFAULT '' COMMENT '주소',
  `cp_item1` varchar(50) NOT NULL DEFAULT '' COMMENT '업태',
  `cp_item2` varchar(50) NOT NULL DEFAULT '' COMMENT '업종',
  `cp_charge` varchar(50) NOT NULL DEFAULT '' COMMENT '담당자',
  `cp_tel` varchar(20) NOT NULL DEFAULT '' COMMENT '전화',
  `cp_tel2` varchar(20) NOT NULL DEFAULT '',
  `cp_fax` varchar(50) NOT NULL DEFAULT '' COMMENT '팩스',
  `cp_delivery_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상품 배송비 정책 사용여부',
  `cp_delivery_price` int(10) NOT NULL DEFAULT 0 COMMENT '배송정보 - 기본배송비',
  `cp_delivery_freeprice` int(10) NOT NULL DEFAULT 0 COMMENT '배송정보 - 무료배송비',
  `cp_delivery_company` varchar(50) NOT NULL DEFAULT '' COMMENT '배송정보 - 지정택배사',
  `cp_delivery_date` varchar(50) NOT NULL DEFAULT '' COMMENT '배송정보 - 평균배송기간',
  `cp_homepage` varchar(100) NOT NULL DEFAULT '' COMMENT '홈페이지',
  `cp_email` varchar(50) NOT NULL DEFAULT '' COMMENT 'email',
  `cp_delivery_complain_price` varchar(100) NOT NULL DEFAULT '' COMMENT '반송/교환배송비',
  `cp_delivery_return_addr` varchar(100) NOT NULL DEFAULT '' COMMENT '반송주소',
  `cp_complain_ok` text NOT NULL COMMENT '교환/반품/환불이 가능한 경우',
  `cp_complain_fail` text NOT NULL COMMENT '교환/반품/환불이 불 가능한 경우',
  `cp_rdate` datetime DEFAULT NULL COMMENT '등록일',
  `cp_del_addprice_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '추가배송비적용여부',
  `cp_del_addprice_use_normal` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '일반배송상품을 무료배송비 이상 구매하여 무료배송이 적용된경우 추가배송비 적용여부',
  `cp_del_addprice_use_product` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상품별배송상품의 추가배송비 적용여부',
  `cp_del_addprice_use_unit` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '개별배송상품의 추가배송비 적용여부',
  `cp_del_addprice_use_free` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '무료배송상품의 추가배송비 적용여부',
  `cp_vat_delivery` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '입점업체 배송비 부가세 설정여부',
  `cp_bank` varchar(100) NOT NULL COMMENT '정산계좌 은행정보',
  `cp_bank_account` varchar(100) NOT NULL COMMENT '정산계좌 계좌번호',
  `cp_bank_name` varchar(100) NOT NULL COMMENT '정산계좌 예금주명',
  PRIMARY KEY (`cp_id`),
  KEY `cp_vat_delivery` (`cp_vat_delivery`),
  KEY `cp_name` (`cp_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='입점업체정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_company`
--

LOCK TABLES `smart_company` WRITE;
/*!40000 ALTER TABLE `smart_company` DISABLE KEYS */;
INSERT INTO `smart_company` VALUES ('submaster','*6BB4837EB74329105EE4568DDA7DC67ED2CA2AD9','사이트','0000-0000','제0000-00000호','홍길동','서울 특별시 강남구 역삼동','서비스','솔루션개발','홍길동','0000-0000','010-0000-0000','000-000-0000','N',2500,150000,'CJ대한통운택배','3','onedaynet.co.kr','help@domain.com','','서울특별시 강남구 역삼동 000-0번지','','','2023-03-29 18:58:23','Y','N','N','Y','Y','Y','','','');
/*!40000 ALTER TABLE `smart_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_currency`
--

DROP TABLE IF EXISTS `smart_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_currency` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `seq` int(11) NOT NULL COMMENT '수집단위별로 구분',
  `curr_type` varchar(5) NOT NULL DEFAULT 'USD' COMMENT '환율종류 (기본: USD)',
  `curr_name` varchar(255) NOT NULL COMMENT '환율이름',
  `curr_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '환율적용일시',
  `curr0` float(7,2) NOT NULL COMMENT '현찰(살때 금액)',
  `curr1` float(7,2) NOT NULL COMMENT '현찰(팔때 금액)',
  `curr2` float(7,2) NOT NULL COMMENT '송금(보낼때 금액)',
  `curr3` float(7,2) NOT NULL COMMENT '송금(받을때 금액)',
  `curr4` float(7,2) NOT NULL COMMENT '환가료율',
  `curr5` float(7,2) NOT NULL COMMENT '매매기준율 (현재환율 사용값)',
  `curr6` float(7,3) NOT NULL COMMENT '미화환산율',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '저장 일시',
  `custom_curr` float(7,2) NOT NULL DEFAULT 0.00 COMMENT '사용자 입력값',
  PRIMARY KEY (`idx`),
  KEY `seq` (`seq`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COMMENT='환율 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_currency`
--

LOCK TABLES `smart_currency` WRITE;
/*!40000 ALTER TABLE `smart_currency` DISABLE KEYS */;
INSERT INTO `smart_currency` VALUES (92,1,'JPY','일본 JPY (100엔)','2017-08-09 15:18:36',1049.84,1013.74,1041.90,1021.68,0.00,1031.79,0.910,'2017-08-09 15:18:36',1049.84),(91,1,'USD','미국 USD','2017-08-09 15:18:36',1154.25,1114.55,1145.50,1123.30,0.00,1134.40,1.000,'2017-08-09 15:18:36',1154.25),(94,2,'USD','미국 USD','2017-08-09 17:37:32',1157.91,1118.09,1149.10,1126.90,0.00,1138.00,1.000,'2017-08-09 17:37:32',1157.91),(93,1,'CNY','중국 CNY','2017-08-09 15:18:36',177.84,160.92,171.07,167.69,0.00,169.38,0.149,'2017-08-09 15:18:36',177.84),(99,3,'CNY','중국 CNY','2017-08-10 16:35:22',179.47,162.39,172.63,169.23,0.00,170.93,0.150,'2017-08-10 16:35:22',179.47),(98,3,'JPY','일본 JPY (100엔)','2017-08-10 16:35:22',1056.12,1019.80,1048.13,1027.79,0.00,1037.96,0.909,'2017-08-10 16:35:22',1056.12),(95,2,'JPY','일본 JPY (100엔)','2017-08-09 17:37:32',1055.48,1019.18,1047.49,1027.17,0.00,1037.33,0.912,'2017-08-09 17:37:32',1055.48),(96,2,'CNY','중국 CNY','2017-08-09 17:37:32',178.47,161.49,171.67,168.29,0.00,169.98,0.149,'2017-08-09 17:37:32',178.47),(97,3,'USD','미국 USD','2017-08-10 16:35:22',1161.47,1121.53,1152.60,1130.40,0.00,1141.50,1.000,'2017-08-10 16:35:22',1161.47);
/*!40000 ALTER TABLE `smart_currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_delivery_addprice`
--

DROP TABLE IF EXISTS `smart_delivery_addprice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_delivery_addprice` (
  `da_uid` int(8) NOT NULL AUTO_INCREMENT,
  `da_addr` varchar(100) NOT NULL COMMENT '지정주소',
  `da_price` int(10) DEFAULT NULL COMMENT '추가배송비',
  `da_rdate` datetime NOT NULL COMMENT '저장일',
  PRIMARY KEY (`da_uid`),
  KEY `da_addr` (`da_addr`)
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8 COMMENT='추가배송비 지역 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_delivery_addprice`
--

LOCK TABLES `smart_delivery_addprice` WRITE;
/*!40000 ALTER TABLE `smart_delivery_addprice` DISABLE KEYS */;
INSERT INTO `smart_delivery_addprice` VALUES (1,'인천 강화군 교동면',3000,'2023-03-28 16:38:17'),(2,'인천 강화군 삼산면',3000,'2023-03-28 16:38:17'),(3,'인천 강화군 서도면',3000,'2023-03-28 16:38:17'),(4,'인천 옹진군 대청면',3000,'2023-03-28 16:38:17'),(5,'인천 옹진군 덕적면',3000,'2023-03-28 16:38:17'),(6,'인천 옹진군 백령면',3000,'2023-03-28 16:38:17'),(7,'인천 옹진군 북도면',3000,'2023-03-28 16:38:17'),(8,'인천 옹진군 연평면',3000,'2023-03-28 16:38:17'),(9,'인천 옹진군 자월면',3000,'2023-03-28 16:38:17'),(10,'인천 중구 무의동',3000,'2023-03-28 16:38:17'),(11,'전북 군산시 옥도면',3000,'2023-03-28 16:38:17'),(12,'전북 부안군 위도면',3000,'2023-03-28 16:38:17'),(13,'부산 강서구 눌차동',3000,'2023-03-28 16:38:17'),(14,'부산 강서구 대항동',3000,'2023-03-28 16:38:17'),(15,'부산 강서구 동선동',3000,'2023-03-28 16:38:17'),(16,'부산 강서구 성북동',3000,'2023-03-28 16:38:17'),(17,'부산 강서구 천성동',3000,'2023-03-28 16:38:17'),(18,'경남 거제시 장목면 시방리',3000,'2023-03-28 16:38:17'),(19,'경남 거제시 둔덕면 술역리',3000,'2023-03-28 16:38:17'),(20,'경남 사천시 마도동',3000,'2023-03-28 16:38:17'),(21,'경남 사천시 신수동',3000,'2023-03-28 16:38:17'),(22,'경남 통영시 사량면',3000,'2023-03-28 16:38:17'),(23,'경남 통영시 욕지면',3000,'2023-03-28 16:38:17'),(24,'경남 통영시 용남면 어의리',3000,'2023-03-28 16:38:17'),(25,'경남 통영시 용남면 지도리',3000,'2023-03-28 16:38:17'),(26,'경남 통영시 한산면',3000,'2023-03-28 16:38:17'),(27,'경남 통영시 산양읍 저림리',3000,'2023-03-28 16:38:17'),(28,'경남 통영시 산양읍 추도리',3000,'2023-03-28 16:38:17'),(29,'경남 통영시 산양읍 연곡리',3000,'2023-03-28 16:38:17'),(30,'경남 통영시 산양읍 곤리',3000,'2023-03-28 16:38:17'),(31,'제주특별자치도 제주시',3000,'2023-03-28 16:38:17'),(32,'제주특별자치도 제주시 우도면',3000,'2023-03-28 16:38:17'),(33,'제주특별자치도 제주시 추자면',3000,'2023-03-28 16:38:17'),(34,'제주특별자치도 서귀포시',3000,'2023-03-28 16:38:17'),(35,'경북 울릉군 북면',3000,'2023-03-28 16:38:17'),(36,'경북 울릉군 서면',3000,'2023-03-28 16:38:17'),(37,'경북 울릉군 울릉읍',3000,'2023-03-28 16:38:17'),(38,'충남 당진시 석문면 난지도리',3000,'2023-03-28 16:38:17'),(39,'충남 당진시 신평면 매산리',3000,'2023-03-28 16:38:17'),(40,'충남 보령시 오천면 고대도리',3000,'2023-03-28 16:38:17'),(41,'충남 보령시 오천면 녹도리',3000,'2023-03-28 16:38:17'),(42,'충남 보령시 오천면 삽시도리',3000,'2023-03-28 16:38:17'),(43,'충남 보령시 오천면 외연도리',3000,'2023-03-28 16:38:17'),(44,'충남 보령시 오천면 원산도리',3000,'2023-03-28 16:38:17'),(45,'충남 보령시 오천면 장고도리',3000,'2023-03-28 16:38:17'),(46,'충남 보령시 오천면 호도리',3000,'2023-03-28 16:38:17'),(47,'충남 보령시 오천면 효자도리',3000,'2023-03-28 16:38:17'),(48,'충남 서산시 지곡면 중왕리',3000,'2023-03-28 16:38:17'),(49,'충남 태안군 근흥면 가의도리',3000,'2023-03-28 16:38:17'),(50,'전남 고흥군 봉래면 사양리',3000,'2023-03-28 16:38:17'),(51,'전남 고흥군 도양읍 시산리',3000,'2023-03-28 16:38:17'),(52,'전남 고흥군 도양읍 봉암리',3000,'2023-03-28 16:38:17'),(53,'전남 고흥군 도양읍 득량리',2500,'2023-03-28 16:38:17'),(54,'전남 고흥군 도화면 지죽리',3000,'2023-03-28 16:38:17'),(55,'전남 목포시 달동',3000,'2023-03-28 16:38:17'),(56,'전남 목포시 율도동',3000,'2023-03-28 16:38:17'),(57,'전남 신안군 도초면',3000,'2023-03-28 16:38:17'),(58,'전남 신안군 비금면',3000,'2023-03-28 16:38:17'),(59,'전남 신안군 신의면',3000,'2023-03-28 16:38:17'),(60,'전남 신안군 안좌면',3000,'2023-03-28 16:38:17'),(61,'전남 신안군 암태면',3000,'2023-03-28 16:38:17'),(62,'전남 신안군 압해읍 가란리',3000,'2023-03-28 16:38:17'),(63,'전남 신안군 압해읍 고이리',3000,'2023-03-28 16:38:17'),(64,'전남 신안군 압해읍 매화리',3000,'2023-03-28 16:38:17'),(65,'전남 신안군 임자면',3000,'2023-03-28 16:38:17'),(66,'전남 신안군 자은면',3000,'2023-03-28 16:38:17'),(67,'전남 신안군 지도읍 어의리',2500,'2023-03-28 16:38:17'),(68,'전남 신안군 지도읍 선도리',2500,'2023-03-28 16:38:17'),(69,'전남 신안군 장산면',2500,'2023-03-28 16:38:17'),(70,'전남 신안군 증도면 병풍리',3000,'2023-03-28 16:38:17'),(71,'전남 신안군 팔금면',3000,'2023-03-28 16:38:17'),(72,'전남 신안군 하의면',3000,'2023-03-28 16:38:17'),(73,'전남 신안군 흑산면',3000,'2023-03-28 16:38:17'),(74,'전남 여수시 경호동',3000,'2023-03-28 16:38:17'),(75,'전남 여수시 남면',3000,'2023-03-28 16:38:17'),(76,'전남 여수시 묘도동',3000,'2023-03-28 16:38:17'),(77,'전남 여수시 삼산면',3000,'2023-03-28 16:38:17'),(78,'전남 여수시 화정면 개도리',3000,'2023-03-28 16:38:17'),(79,'전남 여수시 화정면 낭도리',3000,'2023-03-28 16:38:17'),(80,'전남 여수시 화정면 상화리',3000,'2023-03-28 16:38:17'),(81,'전남 여수시 화정면 여자리',3000,'2023-03-28 16:38:17'),(82,'전남 여수시 화정면 월호리',3000,'2023-03-28 16:38:17'),(83,'전남 여수시 화정면 적금리',3000,'2023-03-28 16:38:17'),(84,'전남 여수시 화정면 제도리',3000,'2023-03-28 16:38:17'),(85,'전남 여수시 화정면 조발리',3000,'2023-03-28 16:38:17'),(86,'전남 여수시 화정면 하화리',3000,'2023-03-28 16:38:17'),(87,'전남 영광군 낙월면',3000,'2023-03-28 16:38:17'),(88,'전남 완도군 군외면 당인리',3000,'2023-03-28 16:38:17'),(89,'전남 완도군 군외면 불목리',3000,'2023-03-28 16:38:17'),(90,'전남 완도군 군외면 영풍리',3000,'2023-03-28 16:38:17'),(91,'전남 완도군 군외면 황진리',3000,'2023-03-28 16:38:17'),(92,'전남 완도군 금당면',3000,'2023-03-28 16:38:17'),(93,'전남 완도군 금일읍',3000,'2023-03-28 16:38:17'),(94,'전남 완도군 약산면',3000,'2023-03-28 16:38:17'),(95,'전남 완도군 고금면',3000,'2023-03-28 16:38:17'),(96,'전남 완도군 노화읍',3000,'2023-03-28 16:38:17'),(97,'전남 완도군 보길면',3000,'2023-03-28 16:38:17'),(98,'전남 완도군 생일면',3000,'2023-03-28 16:38:17'),(99,'전남 완도군 소안면',3000,'2023-03-28 16:38:17'),(100,'전남 완도군 청산면',3000,'2023-03-28 16:38:17'),(101,'전남 진도군 조도면',3000,'2023-03-28 16:38:17'),(102,'전남 진도군 의신면 모도리',5000,'2023-03-28 16:38:17'),(103,'전남 보성군 벌교읍 장도리',5000,'2023-03-28 16:38:17'),(107,'제주특별자치도 서귀포시 가가로',5000,'2023-03-28 16:38:17');
/*!40000 ALTER TABLE `smart_delivery_addprice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_display_main_product`
--

DROP TABLE IF EXISTS `smart_display_main_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_display_main_product` (
  `dmp_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `dmp_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `dmp_dmsuid` int(11) NOT NULL COMMENT '메인진열분류 고유번호',
  `dmp_idx` int(8) unsigned NOT NULL COMMENT '상품등록순위',
  `dmp_sort_group` int(8) NOT NULL DEFAULT 100 COMMENT '우선순위 그룹',
  `dmp_sort_idx` decimal(8,1) NOT NULL COMMENT '우선순위그룹내 순위',
  PRIMARY KEY (`dmp_uid`),
  UNIQUE KEY `dmp_pcode` (`dmp_pcode`,`dmp_dmsuid`)
) ENGINE=MyISAM AUTO_INCREMENT=1049 DEFAULT CHARSET=utf8 COMMENT='선택된 메인상품 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_display_main_product`
--

LOCK TABLES `smart_display_main_product` WRITE;
/*!40000 ALTER TABLE `smart_display_main_product` DISABLE KEYS */;
INSERT INTO `smart_display_main_product` VALUES (227,'I9015-A5703-X4140',3,0,100,0.0),(226,'O4100-X1856-J3448',3,0,100,0.0),(225,'M1859-K2071-J7564',3,0,100,0.0),(217,'W9993-U8576-I6048',3,0,100,0.0),(216,'I9663-I3989-M1758',3,0,100,0.0),(215,'Y1047-Y2987-W3026',3,0,100,0.0),(214,'X4392-M8663-S0997',3,0,100,0.0),(213,'M2268-C3170-A0696',3,0,100,0.0),(212,'U5693-A0426-E2787',3,0,100,0.0),(211,'D4896-C9371-H5713',3,0,100,0.0),(210,'X2712-B7619-T5889',3,0,100,0.0),(209,'R9185-R0383-Z4260',3,0,100,0.0),(205,'O0470-N6231-X7532',3,0,100,0.0),(206,'T1347-I6218-F4622',3,0,100,0.0),(207,'E3505-X2760-W2432',3,0,100,0.0),(208,'Q3292-N4120-L9039',3,0,100,0.0),(224,'U7466-D7645-U7059',3,0,100,0.0),(223,'U7165-H8125-O9655',3,0,100,0.0),(222,'W2881-F6973-Q0064',3,0,100,0.0),(218,'N2933-F5060-X0616',3,0,100,0.0),(219,'S9079-P6916-S2139',3,0,100,0.0),(220,'U8272-O9766-V5747',3,0,100,0.0),(221,'K1568-U1858-O3428',3,0,100,0.0),(468,'E3505-X2760-W2432',11,0,100,0.0),(467,'R9185-R0383-Z4260',12,46,100,46.0),(466,'X2712-B7619-T5889',12,42,100,42.0),(234,'X7438-G9567-O9449',3,0,100,0.0),(465,'Q3292-N4120-L9039',12,41,100,41.0),(464,'X0025-W5794-L8949',12,40,100,40.0),(462,'T1347-I6218-F4622',12,39,100,39.0),(463,'E3505-X2760-W2432',12,38,100,38.0),(233,'Y6080-M6238-X8782',3,0,100,0.0),(232,'U3268-N4835-F4630',3,0,100,0.0),(228,'O6682-M3737-J5198',3,0,100,0.0),(229,'K6148-R4345-T3997',3,0,100,0.0),(230,'A4842-W9813-B0053',3,0,100,0.0),(231,'I0944-U1916-L9938',3,0,100,0.0),(461,'O6730-J6170-N2505',12,37,100,37.0),(460,'O0470-N6231-X7532',12,36,100,36.0),(458,'T1347-I6218-F4622',11,0,100,0.0),(455,'O6730-J6170-N2505',3,0,100,0.0),(456,'O0470-N6231-X7532',11,0,100,0.0),(457,'O6730-J6170-N2505',11,0,100,0.0),(476,'X4392-M8663-S0997',11,0,100,0.0),(153,'E3505-X2760-W2432',4,34,100,34.0),(154,'Y0224-J6073-U3439',4,47,100,47.0),(259,'R9185-R0383-Z4260',6,0,100,0.0),(258,'Q3292-N4120-L9039',6,0,100,0.0),(257,'E3505-X2760-W2432',6,0,100,0.0),(256,'T1347-I6218-F4622',6,0,100,0.0),(255,'O0470-N6231-X7532',6,0,100,0.0),(475,'M2268-C3170-A0696',11,0,100,0.0),(474,'U5693-A0426-E2787',11,0,100,0.0),(473,'D4896-C9371-H5713',11,0,100,0.0),(472,'R9185-R0383-Z4260',11,0,100,0.0),(469,'X0025-W5794-L8949',11,0,100,0.0),(470,'Q3292-N4120-L9039',11,0,100,0.0),(471,'X2712-B7619-T5889',11,0,100,0.0),(485,'U7165-H8125-O9655',11,0,100,0.0),(264,'X4392-M8663-S0997',6,0,100,0.0),(263,'M2268-C3170-A0696',6,0,100,0.0),(262,'U5693-A0426-E2787',6,0,100,0.0),(261,'D4896-C9371-H5713',6,0,100,0.0),(260,'X2712-B7619-T5889',6,0,100,0.0),(496,'X7438-G9567-O9449',11,0,100,0.0),(495,'Y6080-M6238-X8782',11,0,100,0.0),(494,'U3268-N4835-F4630',11,0,100,0.0),(484,'W2881-F6973-Q0064',11,0,100,0.0),(483,'K1568-U1858-O3428',11,0,100,0.0),(482,'U8272-O9766-V5747',11,0,100,0.0),(481,'S9079-P6916-S2139',11,0,100,0.0),(480,'N2933-F5060-X0616',11,0,100,0.0),(477,'Y1047-Y2987-W3026',11,0,100,0.0),(478,'I9663-I3989-M1758',11,0,100,0.0),(479,'W9993-U8576-I6048',11,0,100,0.0),(493,'I0944-U1916-L9938',11,0,100,0.0),(492,'A4842-W9813-B0053',11,0,100,0.0),(491,'K6148-R4345-T3997',11,0,100,0.0),(490,'O6682-M3737-J5198',11,0,100,0.0),(489,'I9015-A5703-X4140',11,0,100,0.0),(486,'U7466-D7645-U7059',11,0,100,0.0),(487,'M1859-K2071-J7564',11,0,100,0.0),(488,'O4100-X1856-J3448',11,0,100,0.0),(502,'S6736-C4656-H6905',11,0,100,0.0),(501,'G4124-W5232-Q8991',11,0,100,0.0),(500,'I9273-B0257-A1645',11,0,100,0.0),(499,'P4006-H8473-O6227',11,0,100,0.0),(498,'A5422-U7450-Z9704',11,0,100,0.0),(497,'D8417-O6480-G6895',11,0,100,0.0),(510,'D7962-C0823-Q5916',11,0,100,0.0),(509,'W4556-P3380-P8691',11,0,100,0.0),(508,'V7541-K6127-I6502',11,0,100,0.0),(507,'J3620-V6262-C6325',11,0,100,0.0),(506,'M0638-E9812-J7753',11,0,100,0.0),(505,'T3871-L1868-Y0284',11,0,100,0.0),(504,'D1985-O9925-Q5265',11,0,100,0.0),(503,'S2985-L7170-L3942',11,0,100,0.0),(305,'U3268-N4835-F4630',7,0,100,0.0),(306,'Q5107-Q0815-P3878',7,0,100,0.0),(307,'P6067-W8317-R6047',7,0,100,0.0),(308,'J8223-U7505-Q4929',7,0,100,0.0),(309,'H7048-G3524-O3544',7,0,100,0.0),(310,'K4377-V3796-M4949',7,0,100,0.0),(311,'I3279-O7366-R6129',7,0,100,0.0),(312,'D2372-H0224-X6293',7,0,100,0.0),(313,'D8087-M0587-K7853',7,0,100,0.0),(314,'M6298-R5682-V6464',7,0,100,0.0),(315,'X8118-A8367-Q5301',7,0,100,0.0),(316,'W2059-D7613-M8352',7,0,100,0.0),(317,'U4196-Y0426-E9150',7,0,100,0.0),(318,'U1491-A8141-W6101',7,0,100,0.0),(319,'T1403-O1025-W4985',7,0,100,0.0),(320,'E0023-T4240-R0822',7,0,100,0.0),(321,'J7862-Z2268-C9228',7,0,100,0.0),(322,'L8188-U6193-U1584',7,0,100,0.0),(323,'D6760-O3726-F5273',7,0,100,0.0),(324,'G4847-Q0899-M7603',7,0,100,0.0),(520,'T1347-I6218-F4622',4,48,100,48.0),(519,'O6730-J6170-N2505',4,49,100,49.0),(518,'O0470-N6231-X7532',4,50,100,50.0),(517,'P0245-S7647-B6578',11,0,100,0.0),(516,'I6754-Z1649-D5555',11,0,100,0.0),(515,'N0272-N1585-A5401',11,0,100,0.0),(514,'M7854-H0794-E8616',11,0,100,0.0),(513,'Q7064-O3733-V2986',11,0,100,0.0),(512,'F6212-L2335-Q9050',11,0,100,0.0),(511,'D9065-S2801-V5045',11,0,100,0.0),(527,'M2268-C3170-A0696',4,51,100,51.0),(526,'U5693-A0426-E2787',4,52,100,52.0),(525,'D4896-C9371-H5713',4,53,100,53.0),(524,'R9185-R0383-Z4260',4,54,100,54.0),(523,'X2712-B7619-T5889',4,55,100,55.0),(522,'Q3292-N4120-L9039',4,56,100,56.0),(521,'X0025-W5794-L8949',4,57,100,57.0),(578,'R9185-R0383-Z4260',13,44,100,44.0),(577,'X2712-B7619-T5889',13,55,100,55.0),(576,'Q3292-N4120-L9039',13,56,100,56.0),(575,'X0025-W5794-L8949',13,57,100,57.0),(574,'E3505-X2760-W2432',13,58,100,58.0),(573,'T1347-I6218-F4622',13,59,100,59.0),(572,'O6730-J6170-N2505',13,60,100,60.0),(571,'O0470-N6231-X7532',13,61,100,61.0),(637,'X4392-M8663-S0997',13,62,100,62.0),(636,'M2268-C3170-A0696',13,54,100,54.0),(628,'R9185-R0383-Z4260',8,0,100,0.0),(627,'X2712-B7619-T5889',8,0,100,0.0),(626,'Q3292-N4120-L9039',8,0,100,0.0),(625,'X0025-W5794-L8949',8,0,100,0.0),(624,'E3505-X2760-W2432',8,0,100,0.0),(623,'T1347-I6218-F4622',8,0,100,0.0),(622,'O6730-J6170-N2505',8,0,100,0.0),(621,'O0470-N6231-X7532',8,0,100,0.0),(645,'W2881-F6973-Q0064',13,53,100,53.0),(644,'K1568-U1858-O3428',13,45,100,45.0),(643,'U8272-O9766-V5747',13,46,100,46.0),(642,'S9079-P6916-S2139',13,47,100,47.0),(641,'N2933-F5060-X0616',13,48,100,48.0),(640,'W9993-U8576-I6048',13,49,100,49.0),(639,'I9663-I3989-M1758',13,50,100,50.0),(638,'Y1047-Y2987-W3026',13,51,100,51.0),(632,'D4896-C9371-H5713',12,35,100,35.0),(633,'D4896-C9371-H5713',8,0,100,0.0),(635,'U5693-A0426-E2787',13,52,100,52.0),(651,'O6682-M3737-J5198',13,63,100,63.0),(650,'I9015-A5703-X4140',13,64,100,64.0),(649,'O4100-X1856-J3448',13,65,100,65.0),(648,'M1859-K2071-J7564',13,76,100,76.0),(647,'U7466-D7645-U7059',13,77,100,77.0),(646,'U7165-H8125-O9655',13,78,100,78.0),(660,'P4006-H8473-O6227',13,79,100,79.0),(659,'A5422-U7450-Z9704',13,80,100,80.0),(658,'D8417-O6480-G6895',13,81,100,81.0),(657,'X7438-G9567-O9449',13,82,100,82.0),(656,'Y6080-M6238-X8782',13,83,100,83.0),(655,'U3268-N4835-F4630',13,75,100,75.0),(654,'I0944-U1916-L9938',13,74,100,74.0),(653,'A4842-W9813-B0053',13,66,100,66.0),(652,'K6148-R4345-T3997',13,67,100,67.0),(669,'V7541-K6127-I6502',13,68,100,68.0),(668,'J3620-V6262-C6325',13,69,100,69.0),(667,'M0638-E9812-J7753',13,70,100,70.0),(666,'T3871-L1868-Y0284',13,71,100,71.0),(665,'D1985-O9925-Q5265',13,72,100,72.0),(664,'S2985-L7170-L3942',13,73,100,73.0),(663,'S6736-C4656-H6905',13,84,100,84.0),(662,'G4124-W5232-Q8991',13,43,100,43.0),(661,'I9273-B0257-A1645',13,11,100,11.0),(405,'E3505-X2760-W2432',10,0,100,0.0),(406,'R9185-R0383-Z4260',10,0,100,0.0),(407,'M2268-C3170-A0696',10,0,100,0.0),(408,'Y1047-Y2987-W3026',10,0,100,0.0),(409,'I0944-U1916-L9938',10,0,100,0.0),(410,'X7438-G9567-O9449',10,0,100,0.0),(411,'I9273-B0257-A1645',10,0,100,0.0),(412,'T3871-L1868-Y0284',10,0,100,0.0),(413,'D9065-S2801-V5045',10,0,100,0.0),(414,'N0272-N1585-A5401',10,0,100,0.0),(415,'J6500-A6364-Q4676',10,0,100,0.0),(416,'X1451-L5781-G4681',10,0,100,0.0),(417,'A3745-C8585-C4607',10,0,100,0.0),(418,'Y6003-W8110-X8793',10,0,100,0.0),(419,'N1968-S0572-D8501',10,0,100,0.0),(420,'X8773-S2828-Z7814',10,0,100,0.0),(421,'X0067-J3026-W2364',10,0,100,0.0),(422,'A2624-P0983-A1168',10,0,100,0.0),(423,'K8067-Z3235-I5827',10,0,100,0.0),(424,'S1034-C7154-R8196',10,0,100,0.0),(425,'X1489-S0468-B1306',10,0,100,0.0),(426,'V8513-U6664-Y3971',10,0,100,0.0),(427,'T2224-R0026-O2050',10,0,100,0.0),(428,'P1968-P5556-B9745',10,0,100,0.0),(429,'X7040-H0823-D7636',10,0,100,0.0),(430,'T9363-Y2364-E0172',10,0,100,0.0),(431,'C7373-G6771-G0341',10,0,100,0.0),(432,'M6298-R5682-V6464',10,0,100,0.0),(433,'O9321-S0490-E3858',10,0,100,0.0),(434,'D0818-G5070-P8359',10,0,100,0.0),(435,'N1542-W8897-K0183',10,0,100,0.0),(436,'I6763-I8350-A7863',10,0,100,0.0),(437,'V6043-P8194-Y8429',10,0,100,0.0),(438,'P6744-E5849-N4173',10,0,100,0.0),(439,'E3120-O2778-S0415',10,0,100,0.0),(440,'C2542-H1911-Y4898',10,0,100,0.0),(441,'C0595-V5937-Y4909',10,0,100,0.0),(442,'V8773-C1313-T1353',10,0,100,0.0),(443,'O9126-M3875-E5651',10,0,100,0.0),(444,'I1904-E5196-G9715',10,0,100,0.0),(445,'N9040-M6565-G9500',10,0,100,0.0),(446,'Q8078-T3716-N7198',10,0,100,0.0),(447,'X3095-S0805-J2625',10,0,100,0.0),(448,'P1697-N6263-R3394',10,0,100,0.0),(449,'P3315-W4110-N7327',10,0,100,0.0),(450,'M7478-A6217-I6469',10,0,100,0.0),(451,'Z1301-F1299-Q6371',10,0,100,0.0),(452,'O7558-O9577-E8125',10,0,100,0.0),(453,'K5520-Q0542-W0282',10,0,100,0.0),(454,'Z9536-S8459-U0110',10,0,100,0.0),(683,'X3414-Z0547-E3153',13,20,100,20.0),(682,'Q4593-W7981-A6679',13,21,100,21.0),(681,'O8283-V5040-T5722',13,22,100,22.0),(634,'D4896-C9371-H5713',13,23,100,23.0),(680,'P5215-X0466-Y4396',13,24,100,24.0),(679,'G0596-Y9754-T7922',13,25,100,25.0),(678,'P0245-S7647-B6578',13,26,100,26.0),(677,'I6754-Z1649-D5555',13,27,100,27.0),(676,'N0272-N1585-A5401',13,19,100,19.0),(675,'M7854-H0794-E8616',13,18,100,18.0),(674,'Q7064-O3733-V2986',13,12,100,12.0),(673,'F6212-L2335-Q9050',13,13,100,13.0),(672,'D9065-S2801-V5045',13,14,100,14.0),(671,'D7962-C0823-Q5916',13,15,100,15.0),(670,'W4556-P3380-P8691',13,16,100,16.0),(1008,'D0738-M0176-Y5381',13,1,100,0.5),(739,'A1352-S9931-R9011',13,17,100,17.0),(1003,'A2939-W4908-C9360',17,16,100,15.5),(710,'A1352-S9931-R9011',12,34,100,34.0),(694,'A1230-M4991-J3859',4,58,100,58.0),(1030,'A2476-K3606-A0582',5,1,100,0.5),(696,'A0005-X0863-B7658',4,46,100,46.0),(697,'A1565-W9572-Y7035',4,45,100,45.0),(1002,'A0851-Y4325-F5506',17,15,100,14.5),(708,'A2624-P0983-A1168',12,43,100,43.0),(700,'A2273-B5415-T0647',5,18,100,18.0),(701,'A1371-M8483-I4297',5,17,100,17.0),(702,'A1812-C9372-W5686',5,16,100,16.0),(703,'A0040-K3768-N6788',5,15,100,15.0),(704,'A2453-Y9662-J6064',5,14,100,14.0),(705,'A3201-F1498-C8030',5,13,100,13.0),(706,'A2286-R9937-E4783',5,12,100,12.0),(1001,'A1561-R7770-B0752',17,14,100,13.5),(1000,'A1853-T6120-V7609',17,13,100,12.5),(999,'B2776-A7254-M2918',18,6,100,6.5),(1007,'U4670-E5063-E9563',13,3,100,2.5),(1006,'D5568-V9700-L4557',13,5,100,4.5),(1005,'C6364-U7256-J4618',13,4,100,3.5),(751,'A1230-M4991-J3859',12,45,100,45.0),(749,'A3026-T2024-G1998',12,44,100,44.0),(997,'S5518-V0905-E2496',18,8,100,8.5),(996,'S9950-E6332-A4864',18,9,100,9.5),(742,'A2624-P0983-A1168',13,28,100,28.0),(755,'A0005-X0863-B7658',13,36,100,36.0),(1004,'A0344-N7288-E3075',13,2,100,1.5),(757,'U6319-W0306-D2554',13,37,100,37.0),(758,'R4965-B1481-Q7587',16,0,100,0.0),(759,'V3848-O7268-M4296',16,0,100,0.0),(760,'V3507-U0233-P6457',16,0,100,0.0),(761,'V6962-L2854-E4178',16,0,100,0.0),(762,'E9864-R4065-A5485',16,0,100,0.0),(763,'X8819-Y0398-Y1301',16,0,100,0.0),(764,'U6319-W0306-D2554',16,0,100,0.0),(765,'V6962-L2854-E4178',13,38,100,38.0),(766,'E9864-R4065-A5485',13,39,100,39.0),(767,'X8819-Y0398-Y1301',13,40,100,40.0),(768,'W8302-S7678-C1342',4,35,100,35.0),(769,'V5595-T9961-G7109',4,36,100,36.0),(770,'V3507-U0233-P6457',5,11,100,11.0),(771,'V6962-L2854-E4178',5,10,100,10.0),(857,'R4965-B1481-Q7587',17,12,100,12.0),(856,'J2672-W4042-X5320',17,11,100,11.0),(855,'R0253-L0354-B7041',17,10,100,10.0),(854,'V8345-Q3967-F5357',17,9,100,9.0),(853,'K2868-D1846-H5430',17,8,100,8.0),(896,'G7003-R2714-U8439',12,33,100,33.0),(898,'I2117-V9375-S1342',12,32,100,32.0),(864,'U2828-N9674-B8348',13,41,100,41.0),(863,'H3282-I9842-V2515',13,42,100,42.0),(862,'S6945-Q5448-U1728',13,35,100,35.0),(861,'J5742-W5935-J9777',13,34,100,34.0),(860,'V6962-L2854-E4178',17,7,100,7.0),(859,'V3507-U0233-P6457',17,6,100,6.0),(858,'V3848-O7268-M4296',17,5,100,5.0),(875,'Z0670-L9448-Y3631',16,0,100,0.0),(874,'Q5312-C0571-K6961',16,0,100,0.0),(873,'I1446-T4942-R1115',16,0,100,0.0),(872,'D4981-A8450-D9316',12,30,100,30.0),(871,'T9446-T1268-H1783',12,29,100,29.0),(870,'J5742-W5935-J9777',12,28,100,28.0),(869,'T5015-B1341-P1790',12,27,100,27.0),(897,'P7967-I3004-N8391',12,26,100,26.0),(867,'S6945-Q5448-U1728',12,25,100,25.0),(884,'X3350-A6035-E5613',16,0,100,0.0),(883,'G7003-R2714-U8439',16,0,100,0.0),(882,'I2117-V9375-S1342',16,0,100,0.0),(881,'P7967-I3004-N8391',16,0,100,0.0),(880,'L8434-F7727-G7666',16,0,100,0.0),(879,'W9923-H5661-Q5488',16,0,100,0.0),(878,'J5742-W5935-J9777',16,0,100,0.0),(877,'Y2395-P6146-S3798',16,0,100,0.0),(876,'L9022-K0408-F9238',16,0,100,0.0),(807,'R4965-B1481-Q7587',12,24,100,24.0),(808,'V3848-O7268-M4296',12,23,100,23.0),(809,'V3507-U0233-P6457',12,22,100,22.0),(810,'V6962-L2854-E4178',12,21,100,21.0),(811,'E9864-R4065-A5485',12,31,100,31.0),(851,'U3243-R3015-K4314',13,29,100,29.0),(850,'V5595-T9961-G7109',13,30,100,30.0),(849,'W8302-S7678-C1342',13,31,100,31.0),(848,'U6319-W0306-D2554',15,0,100,0.0),(847,'X8819-Y0398-Y1301',15,0,100,0.0),(846,'E9864-R4065-A5485',15,0,100,0.0),(845,'V6962-L2854-E4178',15,0,100,0.0),(844,'V3507-U0233-P6457',15,0,100,0.0),(843,'V3848-O7268-M4296',15,0,100,0.0),(842,'R4965-B1481-Q7587',15,0,100,0.0),(892,'F6122-N2589-Z7470',16,0,100,0.0),(891,'A3053-Z2364-W5836',16,0,100,0.0),(890,'J9165-F0246-G4936',16,0,100,0.0),(889,'T9446-T1268-H1783',16,0,100,0.0),(888,'S6945-Q5448-U1728',16,0,100,0.0),(887,'Y8582-D4859-Q3145',16,0,100,0.0),(886,'M6031-S5895-Z1244',16,0,100,0.0),(885,'L7013-Z1148-S1182',16,0,100,0.0),(852,'M1091-A4822-C9827',13,32,100,32.0),(904,'K2354-D9929-E0154',4,37,100,37.0),(903,'X5745-U0589-K1704',4,38,100,38.0),(902,'G9897-T7855-J5021',4,39,100,39.0),(901,'R6712-T1226-L6840',4,40,100,40.0),(900,'T4276-J9716-A4471',4,41,100,41.0),(899,'P7967-I3004-N8391',13,33,100,33.0),(895,'H9960-V9571-Z0112',16,0,100,0.0),(894,'I2084-S8962-T4394',16,0,100,0.0),(893,'Q7766-F3420-T9508',16,0,100,0.0),(905,'A0845-O3760-D3261',4,42,100,42.0),(906,'U1211-J9695-Y6197',4,43,100,43.0),(907,'N3159-X8917-Q4811',4,44,100,44.0),(908,'K2354-D9929-E0154',16,0,100,0.0),(909,'A0845-O3760-D3261',16,0,100,0.0),(910,'U1211-J9695-Y6197',16,0,100,0.0),(911,'N3159-X8917-Q4811',16,0,100,0.0),(982,'W4958-R8595-S3915',12,13,100,13.5),(976,'S6344-C7958-U7531',12,19,100,19.0),(975,'Q3694-F6956-Q8374',12,14,100,14.5),(926,'Z4390-Z3406-Z3738',5,9,100,9.0),(927,'Q3694-F6956-Q8374',5,8,100,8.0),(928,'C2700-I3844-B6480',5,7,100,7.0),(945,'J1401-M5757-B9967',5,6,100,6.0),(930,'E8358-G2654-B3935',17,4,100,4.0),(931,'Z0967-G0045-G8560',17,3,100,3.0),(932,'Z4390-Z3406-Z3738',17,2,100,2.0),(933,'Q3694-F6956-Q8374',17,1,100,1.0),(981,'J6851-A0141-T1461',12,15,100,15.5),(980,'J1401-M5757-B9967',12,16,100,16.5),(979,'T1221-D8774-K5864',12,17,100,17.5),(977,'O6676-G5649-C4842',12,18,100,18.5),(963,'T1221-D8774-K5864',4,24,100,24.0),(962,'C2700-I3844-B6480',4,33,100,32.5),(961,'O6676-G5649-C4842',4,22,100,21.5),(960,'S6344-C7958-U7531',4,21,100,21.0),(959,'Q3694-F6956-Q8374',4,23,100,22.5),(958,'Z4390-Z3406-Z3738',4,25,100,24.5),(957,'Z0967-G0045-G8560',4,26,100,25.5),(956,'E8358-G2654-B3935',4,30,100,30.0),(964,'J1401-M5757-B9967',4,27,100,26.5),(965,'J6851-A0141-T1461',4,28,100,27.5),(966,'W4958-R8595-S3915',4,29,100,28.5),(967,'I7794-Q5336-M3800',4,31,100,30.5),(968,'X5534-Q2867-J6574',4,32,100,31.5),(972,'J1401-M5757-B9967',13,8,100,7.5),(971,'T1221-D8774-K5864',13,7,100,6.5),(970,'C2700-I3844-B6480',13,6,100,5.5),(969,'O6676-G5649-C4842',13,10,100,10.0),(973,'J6851-A0141-T1461',13,9,100,8.5),(978,'C2700-I3844-B6480',12,20,100,20.5),(974,'Z4390-Z3406-Z3738',12,47,100,47.5),(983,'W0678-D3642-R9462',12,3,100,3.5),(984,'U8905-L6749-Y3508',12,4,100,4.5),(985,'C6364-U7256-J4618',12,5,100,5.5),(986,'D5568-V9700-L4557',12,6,100,6.5),(987,'U4670-E5063-E9563',12,7,100,7.5),(988,'D0738-M0176-Y5381',12,8,100,8.5),(989,'Q9820-Q1955-T2594',12,9,100,9.5),(990,'S9950-E6332-A4864',12,10,100,10.5),(991,'S5518-V0905-E2496',12,11,100,11.5),(992,'B9998-B2904-D7348',12,12,100,12.5),(1009,'H7822-Q6889-R6001',4,19,100,18.5),(1010,'Q9820-Q1955-T2594',4,18,100,17.5),(1011,'S9950-E6332-A4864',4,17,100,16.5),(1012,'S5518-V0905-E2496',4,16,100,15.5),(1013,'B9998-B2904-D7348',4,20,100,19.5),(1029,'A1293-C2219-A8817',5,5,100,4.5),(1015,'A2939-W4908-C9360',4,15,100,14.5),(1016,'A2668-K4581-X3347',4,14,100,13.5),(1017,'A1404-E8574-G1535',4,13,100,12.5),(1018,'A3069-L9262-I4831',4,12,100,11.5),(1019,'U4670-E5063-E9563',19,1,100,0.5),(1020,'D0738-M0176-Y5381',19,2,100,1.5),(1021,'Q9820-Q1955-T2594',19,3,100,2.5),(1022,'S5518-V0905-E2496',19,4,100,3.5),(1023,'B9998-B2904-D7348',19,5,100,4.5),(1024,'A3360-Z7477-M6326',4,11,100,10.5),(1025,'W0678-D3642-R9462',4,10,100,9.5),(1026,'U8905-L6749-Y3508',4,9,100,8.5),(1027,'C6364-U7256-J4618',4,8,100,7.5),(1028,'D5568-V9700-L4557',4,7,100,6.5),(1031,'P9352-A0491-I1021',4,5,100,5.0),(1032,'U4670-E5063-E9563',18,3,100,3.5),(1033,'H7822-Q6889-R6001',18,4,100,4.5),(1034,'D0738-M0176-Y5381',18,5,100,5.5),(1035,'A1404-E8574-G1535',18,1,100,1.5),(1038,'A0344-N7288-E3075',18,2,100,2.5),(1039,'B5485-Q8863-C2763',4,6,100,5.5),(1040,'Z7913-S9943-R3804',4,4,100,3.5),(1041,'P3225-K4000-X8414',4,3,100,2.5),(1043,'P3225-K4000-X8414',12,1,100,1.5),(1047,'X5785-V0661-C5698',5,3,100,2.5),(1046,'X5758-P1862-F7949',5,2,100,1.5),(1048,'S3629-N2275-E9290',5,4,100,3.5);
/*!40000 ALTER TABLE `smart_display_main_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_display_main_set`
--

DROP TABLE IF EXISTS `smart_display_main_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_display_main_set` (
  `dms_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `dms_name` varchar(255) NOT NULL COMMENT '메인상품분류명()',
  `dms_view` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '노출여부(Y:노출,N: 비노출)',
  `dms_depth` int(11) NOT NULL DEFAULT 1 COMMENT '차수',
  `dms_parent` varchar(200) NOT NULL COMMENT '부모(2차만 존재)',
  `dms_idx` int(11) NOT NULL COMMENT '정렬순서 (1차는 고정)',
  `dms_list_product_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'PC상품 노출여부',
  `dms_list_product_mobile_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'MOBILE 상품 노출여부',
  `dms_list_product_display` int(11) NOT NULL DEFAULT 4 COMMENT '메인진열상품 목록 상품진열 개수 (PC)',
  `dms_list_product_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '메인진열상품 목록 상품진열 개수 (모바일)',
  `dms_type` enum('md','main') NOT NULL DEFAULT 'md' COMMENT '1차에 한해서 고정',
  `dms_list_product_mobile_limit` int(11) NOT NULL DEFAULT 20 COMMENT '메인진열상품 최대 노출 개수',
  `dms_main_product_display` int(11) NOT NULL DEFAULT 4 COMMENT '메인진열상품 메인 상품진열 개수(PC)',
  `dms_main_product_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '메인진열상품 메인 상품진열 개수(모바일)',
  `dms_main_product_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '메인진열상품 메인 상품진열 타입(box:박스형,list:리스트형)',
  `dms_list_product_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '메인진열상품 목록 상품진열 타입(box:박스형,list:리스트형)',
  PRIMARY KEY (`dms_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='상품진열관리 > 메인상품관리 분류테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_display_main_set`
--

LOCK TABLES `smart_display_main_set` WRITE;
/*!40000 ALTER TABLE `smart_display_main_set` DISABLE KEYS */;
INSERT INTO `smart_display_main_set` VALUES (1,'MD\\\'s Pick','Y',1,'0',1,'Y','Y',4,3,'md',20,4,3,'box','box'),(2,'메인상품분류','Y',1,'0',2,'Y','Y',4,3,'main',20,4,3,'box','box'),(13,'달달 마카롱','Y',2,'1',4,'Y','Y',3,2,'md',20,3,2,'box','box'),(4,'파티 케이크','Y',2,'1',3,'Y','Y',3,3,'md',20,4,3,'box','box'),(5,'상큼한 디저트','Y',2,'1',5,'Y','Y',4,3,'md',20,5,3,'box','box'),(12,'New Arrival ','Y',2,'2',2,'Y','Y',5,3,'main',12,0,0,'box','box'),(17,'선물용 쿠키','Y',2,'1',6,'Y','Y',4,3,'md',20,4,3,'box','box'),(18,'Hot Sale','Y',2,'2',3,'Y','Y',4,2,'main',20,0,0,'box','box'),(19,'수제 유기농 브레드','Y',2,'1',7,'N','Y',4,3,'md',20,4,3,'box','box');
/*!40000 ALTER TABLE `smart_display_main_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_display_type_product`
--

DROP TABLE IF EXISTS `smart_display_type_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_display_type_product` (
  `dtp_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `dtp_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `dtp_dtsuid` int(11) NOT NULL COMMENT '메인진열분류 고유번호',
  `dtp_idx` int(8) unsigned NOT NULL COMMENT '상품등록순위',
  `dtp_sort_group` int(8) NOT NULL DEFAULT 100 COMMENT '우선순위 그룹',
  `dtp_sort_idx` decimal(8,1) NOT NULL COMMENT '우선순위그룹내 순위',
  PRIMARY KEY (`dtp_uid`),
  UNIQUE KEY `dtp_pcode` (`dtp_pcode`,`dtp_dtsuid`)
) ENGINE=MyISAM AUTO_INCREMENT=260 DEFAULT CHARSET=utf8 COMMENT='선택된 타입상품 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_display_type_product`
--

LOCK TABLES `smart_display_type_product` WRITE;
/*!40000 ALTER TABLE `smart_display_type_product` DISABLE KEYS */;
INSERT INTO `smart_display_type_product` VALUES (60,'E3505-X2760-W2432',3,0,100,0.0),(59,'O0470-N6231-X7532',3,0,100,0.0),(58,'S6736-C4656-H6905',2,0,100,0.0),(57,'O6682-M3737-J5198',2,0,100,0.0),(56,'M1859-K2071-J7564',2,0,100,0.0),(55,'I9663-I3989-M1758',2,0,100,0.0),(54,'Q3292-N4120-L9039',1,73,100,73.0),(53,'E3505-X2760-W2432',1,53,100,53.0),(52,'T1347-I6218-F4622',1,52,100,52.0),(51,'O0470-N6231-X7532',1,51,100,51.0),(65,'O0470-N6231-X7532',6,0,100,0.0),(64,'T1347-I6218-F4622',4,34,100,34.0),(63,'O0470-N6231-X7532',4,37,100,37.0),(62,'D4896-C9371-H5713',3,0,100,0.0),(61,'Q3292-N4120-L9039',3,0,100,0.0),(66,'A1561-R7770-B0752',2,0,100,0.0),(67,'A1853-T6120-V7609',2,0,100,0.0),(68,'A0369-A2723-E5596',2,0,100,0.0),(69,'A0005-X0863-B7658',2,0,100,0.0),(70,'A1565-W9572-Y7035',2,0,100,0.0),(71,'A2846-Q8684-N5066',2,0,100,0.0),(72,'A1245-L2995-F9638',2,0,100,0.0),(73,'A1245-L2995-F9638',1,50,100,50.0),(74,'A0515-Q4337-S4413',1,49,100,49.0),(75,'A2273-B5415-T0647',1,48,100,48.0),(76,'A1371-M8483-I4297',1,47,100,47.0),(77,'A1812-C9372-W5686',1,46,100,46.0),(78,'A0040-K3768-N6788',1,45,100,45.0),(79,'A2453-Y9662-J6064',1,44,100,44.0),(80,'A3201-F1498-C8030',1,43,100,43.0),(81,'A2286-R9937-E4783',1,42,100,42.0),(82,'A1293-C2219-A8817',1,41,100,41.0),(83,'A1352-S9931-R9011',3,0,100,0.0),(84,'A3069-L9262-I4831',3,0,100,0.0),(85,'A2668-K4581-X3347',3,0,100,0.0),(86,'A1371-M8483-I4297',3,0,100,0.0),(87,'A2453-Y9662-J6064',3,0,100,0.0),(88,'A3201-F1498-C8030',3,0,100,0.0),(89,'A1293-C2219-A8817',3,0,100,0.0),(90,'S6945-Q5448-U1728',1,40,100,40.0),(91,'T9446-T1268-H1783',1,39,100,39.0),(92,'W6787-B8823-Q9477',1,38,100,38.0),(93,'X1787-T6633-U3494',1,54,100,54.0),(94,'X2983-U3101-P2999',1,55,100,55.0),(95,'J0955-I5419-T7833',1,56,100,56.0),(96,'K3034-T0236-C3404',1,72,100,72.0),(97,'B5143-Z8843-O9559',1,71,100,71.0),(98,'S1916-Q8668-Y8918',1,70,100,70.0),(99,'M3477-K5446-N2008',1,69,100,69.0),(100,'D1356-S9903-F1100',1,68,100,68.0),(101,'G3249-V6010-T3466',1,67,100,67.0),(102,'L6353-M3332-B8893',1,66,100,66.0),(103,'J9165-F0246-G4936',1,65,100,65.0),(104,'V7681-B6129-U3223',1,64,100,64.0),(105,'S6471-A0742-R1954',1,63,100,63.0),(106,'H3282-I9842-V2515',1,62,100,62.0),(107,'P3247-A4888-G3627',1,61,100,61.0),(108,'A1908-M8284-F7366',1,60,100,60.0),(109,'Q6837-C1833-N8932',1,59,100,59.0),(110,'U6065-T4359-W9491',1,58,100,58.0),(111,'R6712-T1226-L6840',1,57,100,57.0),(112,'G9897-T7855-J5021',1,37,100,37.0),(113,'K5460-Z0451-J2949',1,36,100,36.0),(114,'O9678-W0775-A5866',1,25,100,25.0),(115,'Z8245-K4518-T7063',1,24,100,24.0),(116,'J7060-V0887-M6954',1,23,100,23.0),(117,'K7260-K6269-A2025',1,22,100,22.0),(118,'G4064-J2329-S4167',1,21,100,21.0),(119,'A3053-Z2364-W5836',1,20,100,20.0),(120,'C3303-O8863-E2930',1,19,100,19.0),(121,'A0598-S9715-U3414',1,18,100,18.0),(122,'D9901-V5153-N5180',1,17,100,17.0),(123,'D4981-A8450-D9316',1,16,100,16.0),(124,'U2828-N9674-B8348',1,15,100,15.0),(125,'A1760-Y3426-K3977',1,14,100,14.0),(126,'Q5400-A7492-S6387',1,13,100,13.0),(127,'F6122-N2589-Z7470',1,12,100,12.0),(128,'D0516-A5937-V0197',1,11,100,11.0),(129,'Z1414-S6725-R1219',1,10,100,10.0),(130,'N5237-K7075-U1074',1,26,100,26.0),(131,'A8221-Z1238-P0232',1,27,100,27.0),(132,'F9821-L2387-M4923',1,28,100,28.0),(133,'P8227-Z0364-T3723',1,35,100,35.0),(134,'T5015-B1341-P1790',1,34,100,34.0),(135,'O1466-R8243-D9497',1,33,100,33.0),(136,'Q7766-F3420-T9508',1,32,100,32.0),(137,'C9550-B2774-F4645',1,31,100,31.0),(138,'I2084-S8962-T4394',1,30,100,30.0),(139,'H9960-V9571-Z0112',1,29,100,29.0),(140,'S6945-Q5448-U1728',4,38,100,38.0),(168,'N3159-X8917-Q4811',4,39,100,39.0),(167,'U1211-J9695-Y6197',4,40,100,40.0),(145,'S6945-Q5448-U1728',7,8,100,8.0),(146,'T9446-T1268-H1783',7,17,100,17.0),(147,'V7681-B6129-U3223',7,16,100,16.0),(148,'S6471-A0742-R1954',7,15,100,15.0),(149,'H3282-I9842-V2515',7,14,100,14.0),(150,'T5015-B1341-P1790',7,13,100,13.0),(151,'C9550-B2774-F4645',7,12,100,12.0),(152,'J5742-W5935-J9777',7,11,100,11.0),(153,'P7967-I3004-N8391',7,10,100,10.0),(154,'I2117-V9375-S1342',7,9,100,9.0),(166,'A0845-O3760-D3261',4,41,100,41.0),(165,'K2354-D9929-E0154',4,42,100,42.0),(164,'X5745-U0589-K1704',4,43,100,43.0),(163,'G9897-T7855-J5021',4,44,100,44.0),(162,'R6712-T1226-L6840',4,36,100,36.0),(161,'T4276-J9716-A4471',4,35,100,35.0),(211,'J6851-A0141-T1461',4,33,100,32.5),(221,'J1401-M5757-B9967',1,2,100,1.5),(220,'T1221-D8774-K5864',1,3,100,2.5),(219,'C2700-I3844-B6480',1,4,100,3.5),(218,'O6676-G5649-C4842',1,5,100,4.5),(217,'S6344-C7958-U7531',1,6,100,5.5),(226,'C2700-I3844-B6480',7,7,100,6.5),(225,'O6676-G5649-C4842',7,5,100,5.5),(210,'J1401-M5757-B9967',4,45,100,44.5),(209,'T1221-D8774-K5864',4,26,100,25.5),(208,'C2700-I3844-B6480',4,27,100,26.5),(207,'O6676-G5649-C4842',4,28,100,27.5),(206,'S6344-C7958-U7531',4,29,100,28.5),(205,'Q3694-F6956-Q8374',4,30,100,29.5),(204,'Z4390-Z3406-Z3738',4,46,100,46.0),(203,'Z0967-G0045-G8560',4,31,100,30.5),(202,'E8358-G2654-B3935',4,32,100,31.5),(212,'W4958-R8595-S3915',4,23,100,22.5),(213,'E8358-G2654-B3935',1,7,100,6.5),(214,'Z0967-G0045-G8560',1,8,100,7.5),(215,'Z4390-Z3406-Z3738',1,9,100,8.5),(216,'Q3694-F6956-Q8374',1,1,100,0.5),(223,'Z4390-Z3406-Z3738',7,4,100,4.5),(224,'S6344-C7958-U7531',7,6,100,6.0),(222,'Z0967-G0045-G8560',7,3,100,2.5),(227,'T1221-D8774-K5864',7,2,100,1.5),(228,'J1401-M5757-B9967',7,1,100,0.5),(235,'W4958-R8595-S3915',8,4,100,3.5),(234,'J6851-A0141-T1461',8,5,100,4.5),(233,'J1401-M5757-B9967',8,6,100,5.5),(232,'T1221-D8774-K5864',8,7,100,6.5),(231,'C2700-I3844-B6480',8,1,100,1.0),(230,'Z4390-Z3406-Z3738',8,2,100,1.5),(229,'Z0967-G0045-G8560',8,3,100,3.0),(236,'A1293-C2219-A8817',4,22,100,21.5),(237,'A0369-A2723-E5596',4,10,100,9.5),(238,'A0344-N7288-E3075',4,9,100,8.5),(239,'A2476-K3606-A0582',4,8,100,7.5),(240,'A1853-T6120-V7609',4,7,100,6.5),(241,'A1561-R7770-B0752',4,6,100,5.5),(242,'A0851-Y4325-F5506',4,25,100,24.5),(243,'A3360-Z7477-M6326',4,5,100,4.5),(244,'A2939-W4908-C9360',4,4,100,3.5),(245,'A2668-K4581-X3347',4,3,100,2.5),(246,'A1404-E8574-G1535',4,2,100,1.5),(247,'A3069-L9262-I4831',4,11,100,10.5),(248,'H7822-Q6889-R6001',4,12,100,11.5),(249,'W0678-D3642-R9462',4,21,100,20.5),(250,'U8905-L6749-Y3508',4,20,100,19.5),(251,'C6364-U7256-J4618',4,19,100,18.5),(252,'D5568-V9700-L4557',4,18,100,17.5),(253,'U4670-E5063-E9563',4,17,100,16.5),(254,'D0738-M0176-Y5381',4,24,100,23.5),(255,'Q9820-Q1955-T2594',4,16,100,15.5),(256,'S9950-E6332-A4864',4,15,100,14.5),(257,'S5518-V0905-E2496',4,14,100,13.5),(258,'B9998-B2904-D7348',4,13,100,12.5),(259,'B2776-A7254-M2918',4,1,100,0.5);
/*!40000 ALTER TABLE `smart_display_type_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_display_type_set`
--

DROP TABLE IF EXISTS `smart_display_type_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_display_type_set` (
  `dts_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `dts_name` varchar(255) NOT NULL COMMENT '상품분류명()',
  `dts_view` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '노출여부(Y:노출,N: 비노출)',
  `dts_depth` int(11) NOT NULL DEFAULT 1 COMMENT '차수',
  `dts_parent` varchar(200) NOT NULL COMMENT '부모(2차만 존재)',
  `dts_idx` int(11) NOT NULL COMMENT '정렬순서 (1차는 고정)',
  `dts_list_product_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'PC상품 노출여부',
  `dts_list_product_mobile_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'MOBILE 상품 노출여부',
  `dts_list_product_display` int(11) NOT NULL DEFAULT 4 COMMENT 'PC상품진열',
  `dts_list_product_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT 'MOBILE상품진열',
  `dts_type` enum('md','main') NOT NULL DEFAULT 'md' COMMENT '1차에 한해서 고정',
  `dts_img_top_banner_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상단 탑배너',
  `dts_img_top_banner` varchar(50) NOT NULL COMMENT '배너',
  `dts_img_top_banner_target` enum('_none','_self','_blank') NOT NULL DEFAULT '_self' COMMENT '배너 링크 타켓',
  `dts_img_top_banner_link` varchar(200) NOT NULL COMMENT '상단배너 활성화여부',
  `dts_img_top_mobile_banner_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '모바일 상단 배너 사용여부',
  `dts_img_top_mobile_banner` varchar(50) NOT NULL COMMENT '모바일 상단 배너 ',
  `dts_img_top_mobile_banner_target` enum('_none','_self','_blank') NOT NULL DEFAULT '_self' COMMENT '모바일 상단 배너 링크 타겟',
  `dts_img_top_mobile_banner_link` varchar(200) NOT NULL COMMENT '모바일 상단 링크',
  `dts_list_limit` int(10) NOT NULL COMMENT '타입별 상품 페이지당 노출 개수',
  `dts_list_product_display_type` enum('box','list') DEFAULT 'box' COMMENT '타입별 상품 진열설정 타입 (box 박스형, list 리스트형)',
  PRIMARY KEY (`dts_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='상품진열관리 > 타입별 상품관리 분류테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_display_type_set`
--

LOCK TABLES `smart_display_type_set` WRITE;
/*!40000 ALTER TABLE `smart_display_type_set` DISABLE KEYS */;
INSERT INTO `smart_display_type_set` VALUES (1,'NEW','Y',2,'9',4,'Y','Y',4,1,'md','N','','_self','','','','','',0,'box'),(4,'BEST','Y',2,'9',2,'Y','Y',4,2,'md','N','1156886023.jpg','_self','','','','','',30,'box'),(7,'ON SALE','Y',2,'9',6,'Y','Y',4,1,'md','N','','_self','','N','','_self','',0,'box'),(8,'HOT','Y',2,'9',7,'Y','Y',4,1,'md','N','','_self','','N','','_self','',0,'box'),(9,'SPECIAL','Y',1,'0',8,'','',4,1,'','','','','','N','','','',24,'box');
/*!40000 ALTER TABLE `smart_display_type_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_editor_images_files`
--

DROP TABLE IF EXISTS `smart_editor_images_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_editor_images_files` (
  `eif_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '에디터 이미지 파일 고유번호',
  `eif_img` varchar(200) NOT NULL COMMENT '에디터 이미지 파일명',
  `eif_rdate` datetime NOT NULL COMMENT '에디터 이미지 파일 등록일',
  `eif_use_cnt` int(10) NOT NULL COMMENT '에디터 이미지 사용처 개수',
  PRIMARY KEY (`eif_uid`),
  UNIQUE KEY `eif_img` (`eif_img`)
) ENGINE=MyISAM AUTO_INCREMENT=306 DEFAULT CHARSET=utf8 COMMENT='에디터 이미지 파일 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_editor_images_files`
--

LOCK TABLES `smart_editor_images_files` WRITE;
/*!40000 ALTER TABLE `smart_editor_images_files` DISABLE KEYS */;
INSERT INTO `smart_editor_images_files` VALUES (96,'202212161503471433267848.jpg','2023-09-13 15:38:23',26),(83,'202205171520421108711234.jpg','2023-02-03 16:24:39',2),(81,'202203241101001969457837.jpg','2023-02-03 16:24:39',1),(89,'202206201146431259324814.jpg','2023-02-03 16:24:39',1),(300,'202303201111511176330953.jpg','2023-03-20 11:11:51',1),(301,'2023032011122071211653.jpg','2023-03-20 11:12:20',1),(285,'202303071634251566035562.jpg','2023-03-07 16:34:25',1),(281,'202302231516141087769167.jpg','2023-03-02 08:53:50',1),(278,'3996494211_1676816631.jpg','2023-02-26 12:46:46',1);
/*!40000 ALTER TABLE `smart_editor_images_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_editor_images_use`
--

DROP TABLE IF EXISTS `smart_editor_images_use`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_editor_images_use` (
  `eiu_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '에디터 이미지 사용관리 고유번호',
  `eiu_tablename` varchar(50) NOT NULL DEFAULT '' COMMENT '대상 테이블명',
  `eiu_datauid` varchar(50) DEFAULT NULL COMMENT '대상 데이터 고유번호',
  `eiu_eifuid` int(20) DEFAULT NULL COMMENT '에디터이미지 파일 uid',
  `eiu_dummy` tinyint(1) NOT NULL DEFAULT 0 COMMENT '더미',
  PRIMARY KEY (`eiu_uid`),
  UNIQUE KEY `eiu_unique` (`eiu_tablename`,`eiu_datauid`,`eiu_eifuid`),
  KEY `eiu_eifuid` (`eiu_eifuid`),
  KEY `eiu_tablename` (`eiu_tablename`),
  KEY `eiu_datauid` (`eiu_datauid`)
) ENGINE=MyISAM AUTO_INCREMENT=243 DEFAULT CHARSET=utf8 COMMENT='에디터 이미지 사용처 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_editor_images_use`
--

LOCK TABLES `smart_editor_images_use` WRITE;
/*!40000 ALTER TABLE `smart_editor_images_use` DISABLE KEYS */;
INSERT INTO `smart_editor_images_use` VALUES (1,'normal','3',7,0),(2,'product','X5534-Q2867-J6574',13,0),(3,'product','E8358-G2654-B3935',14,0),(4,'product','Z0967-G0045-G8560',15,0),(5,'product','Z4390-Z3406-Z3738',16,0),(6,'product','Q3694-F6956-Q8374',17,0),(7,'product','S6344-C7958-U7531',18,0),(8,'product','O6676-G5649-C4842',19,0),(9,'product','C2700-I3844-B6480',20,0),(10,'product','T1221-D8774-K5864',21,0),(11,'product','J1401-M5757-B9967',22,0),(12,'product','J6851-A0141-T1461',23,0),(13,'product','W4958-R8595-S3915',24,0),(14,'product','I7794-Q5336-M3800',25,0),(34,'board','18',83,0),(213,'promotion','14',285,0),(216,'product','A1561-R7770-B0752',96,0),(217,'product','A3360-Z7477-M6326',96,0),(218,'product','A2668-K4581-X3347',96,0),(219,'product','A2476-K3606-A0582',96,0),(220,'product','A0344-N7288-E3075',96,0),(46,'board','28',89,0),(222,'product','U4670-E5063-E9563',96,0),(215,'product','A3069-L9262-I4831',96,0),(233,'normal','5',300,0),(32,'board','14',81,0),(221,'product','D5568-V9700-L4557',96,0),(210,'product','A0369-A2723-E5596',96,0),(209,'product','U8905-L6749-Y3508',96,0),(208,'product','A1404-E8574-G1535',96,0),(207,'product','A2939-W4908-C9360',96,0),(206,'product','A0851-Y4325-F5506',96,0),(204,'product','S5518-V0905-E2496',96,0),(205,'product','H7822-Q6889-R6001',96,0),(202,'product','A1853-T6120-V7609',96,0),(201,'mailing','16',281,0),(200,'mailing','15',278,0),(199,'product','U8131-A5406-B1395',96,0),(198,'product','A1293-C2219-A8817',96,0),(197,'product','W0678-D3642-R9462',96,0),(196,'product','C6364-U7256-J4618',96,0),(195,'product','B9998-B2904-D7348',96,0),(194,'product','B2776-A7254-M2918',96,0),(234,'normal','3',301,0),(139,'board','47',83,0),(223,'product','D0738-M0176-Y5381',96,0),(224,'product','Q9820-Q1955-T2594',96,0),(225,'product','S9950-E6332-A4864',96,0),(226,'product','X8000-P0254-T3646',96,0);
/*!40000 ALTER TABLE `smart_editor_images_use` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_favmenu`
--

DROP TABLE IF EXISTS `smart_favmenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_favmenu` (
  `fm_uid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `fm_appId` varchar(50) NOT NULL COMMENT '자주 사용하는 메뉴',
  `fm_admin` int(11) NOT NULL COMMENT '자주쓰는메뉴를 사용하는 관리자 uid',
  `fm_menuName` varchar(50) NOT NULL COMMENT '메뉴이름',
  `fm_menu` int(11) NOT NULL COMMENT '메뉴 uid',
  `fm_menuIdx` int(10) unsigned DEFAULT NULL COMMENT '메뉴번호',
  `fm_depth` tinyint(4) NOT NULL DEFAULT 1 COMMENT '자주쓰는메뉴 차수',
  `fm_parent` int(8) NOT NULL COMMENT '2차 메뉴일경우 부모 uid',
  `fm_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '자주쓰는메뉴 노출여부',
  `fm_rdate` datetime DEFAULT NULL COMMENT '생성일',
  PRIMARY KEY (`fm_uid`),
  KEY `fm_appId` (`fm_appId`),
  KEY `fm_adimin` (`fm_admin`),
  KEY `fm_depth` (`fm_depth`),
  KEY `fm_parent` (`fm_parent`),
  KEY `fm_view` (`fm_view`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COMMENT='자주사용하는 메뉴';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_favmenu`
--

LOCK TABLES `smart_favmenu` WRITE;
/*!40000 ALTER TABLE `smart_favmenu` DISABLE KEYS */;
INSERT INTO `smart_favmenu` VALUES (7,'totaladmin',1,'주문/배송',0,2,1,0,'Y','2023-02-07 11:11:00'),(59,'totaladmin',1,'배송주문상품관리',245,5,2,7,'Y','2023-02-07 11:11:00'),(11,'totaladmin',1,'문의답변',0,1,1,0,'Y','2023-02-07 11:11:00'),(84,'totaladmin',1,'상품리뷰',285,3,2,11,'Y','2023-02-07 11:11:00'),(85,'totaladmin',1,'상품문의',286,5,2,11,'Y','2023-02-07 11:11:00'),(58,'totaladmin',1,'배송주문관리',103,0,2,7,'Y','2023-02-07 11:11:00'),(54,'totaladmin',1,'1:1문의 관리',283,4,2,11,'Y','2023-02-07 11:11:00');
/*!40000 ALTER TABLE `smart_favmenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_feed`
--

DROP TABLE IF EXISTS `smart_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_feed` (
  `f_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `f_email` varchar(20) NOT NULL DEFAULT '' COMMENT '구독 이메일',
  `f_emailsend` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '메일수신여부',
  `f_rdate` datetime NOT NULL COMMENT '구독신청일시',
  PRIMARY KEY (`f_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='구독신청 이메일';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_feed`
--

LOCK TABLES `smart_feed` WRITE;
/*!40000 ALTER TABLE `smart_feed` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_feed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_files`
--

DROP TABLE IF EXISTS `smart_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_files` (
  `f_uid` int(11) NOT NULL AUTO_INCREMENT,
  `f_table` varchar(255) DEFAULT NULL COMMENT 'db테이블',
  `f_table_uid` varchar(50) NOT NULL COMMENT '테이블에서 고유번호',
  `f_oldname` varchar(255) NOT NULL COMMENT '업로드시 파일명',
  `f_realname` varchar(255) NOT NULL COMMENT '저장시 파일명',
  `f_size` int(11) NOT NULL COMMENT '바이트단위',
  `f_download` int(11) NOT NULL DEFAULT 0 COMMENT '다운로드 횟수',
  `f_mdate` datetime DEFAULT NULL COMMENT '최근수정일',
  `f_rdate` datetime NOT NULL,
  PRIMARY KEY (`f_uid`),
  KEY `bf_biuid` (`f_table_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='업로드 파일관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_files`
--

LOCK TABLES `smart_files` WRITE;
/*!40000 ALTER TABLE `smart_files` DISABLE KEYS */;
INSERT INTO `smart_files` VALUES (10,'smart_bbs','24','item_9.jpg','467047268.jpg',154848,1,'2022-06-20 11:42:35','2022-06-20 11:42:35'),(34,'smart_request','2_user','ma_review_4.jpg','688639617.jpg',43767,0,'2023-02-23 17:36:06','2023-02-23 17:36:06'),(35,'smart_request','2_user','sub_top_bn2_1.jpg','1975269359.jpg',340900,1,'2023-02-23 17:36:06','2023-02-23 17:36:06');
/*!40000 ALTER TABLE `smart_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_individual`
--

DROP TABLE IF EXISTS `smart_individual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_individual` (
  `in_id` varchar(30) NOT NULL DEFAULT '',
  `in_pw` varchar(50) NOT NULL COMMENT '비밀번호',
  `in_name` varchar(30) NOT NULL COMMENT '이름',
  `in_staff` char(10) NOT NULL DEFAULT '' COMMENT 'md : MD, NULL : 일반회원',
  `in_birth` date DEFAULT NULL COMMENT '생년월일',
  `in_sex` enum('M','F','') NOT NULL DEFAULT '' COMMENT '성별',
  `in_email` varchar(50) NOT NULL COMMENT '이메일',
  `in_tel` varchar(20) DEFAULT NULL COMMENT '전화',
  `in_tel2` varchar(20) NOT NULL COMMENT '핸드폰',
  `in_zip1` char(3) NOT NULL DEFAULT '' COMMENT '우편번호1',
  `in_zip2` char(3) NOT NULL DEFAULT '' COMMENT '우편번호2',
  `in_zonecode` varchar(10) NOT NULL COMMENT '국가기초구역번호',
  `in_address1` varchar(255) NOT NULL DEFAULT '' COMMENT '주소1',
  `in_address2` varchar(255) NOT NULL DEFAULT '' COMMENT '주소2',
  `in_address_doro` varchar(255) NOT NULL COMMENT '도로명주소',
  `in_emailsend` enum('Y','N') DEFAULT 'Y' COMMENT '이메일 수신여부',
  `in_smssend` enum('Y','N') DEFAULT 'Y' COMMENT '문자 수신여부',
  `in_rdate` datetime DEFAULT NULL COMMENT '가입일',
  `in_mdate` datetime DEFAULT NULL COMMENT '수정일',
  `in_ldate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '마지막 로그인',
  `in_out` enum('Y','N') DEFAULT 'N' COMMENT '탈퇴여부 - Y 일경우 탈퇴',
  `in_odate` datetime NOT NULL COMMENT '회원 탈퇴일',
  `in_ip` varchar(20) DEFAULT NULL,
  `in_point` int(10) NOT NULL DEFAULT 0 COMMENT '회원 소유 포인트',
  `in_userlevel` char(2) NOT NULL DEFAULT '2' COMMENT '회원레벨 - 일반회원:2 , 관리자:9',
  `in_sleep_type` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '휴면전환타입',
  `in_sleep_request` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '휴면회원일 시 로그인시 인증되도록 요청(Y: 로그인시 휴면해제, N 로그인 시 휴면인증 페이지 이동)',
  `in_pw_rdate` datetime NOT NULL COMMENT '패스워드 변경일',
  `in_cancel_bank` varchar(100) NOT NULL COMMENT '환불은행',
  `in_cancel_bank_name` varchar(100) NOT NULL COMMENT '환불예금주',
  `in_cancel_bank_account` varchar(100) NOT NULL COMMENT '환불계좌번호',
  `m_opt_date` datetime NOT NULL COMMENT '수신동의/거부일자',
  `auth_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '본인인증여부',
  `auth_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '본인인증일시',
  `sns_join` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '소셜가입(연동)여부',
  `fb_join` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '페북 가입',
  `fb_encid` varchar(255) NOT NULL COMMENT '페북 고유아이디',
  `ko_join` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '카카오 가입',
  `ko_encid` varchar(255) NOT NULL COMMENT '카카오 고유아이디',
  `nv_join` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '네이버 가입',
  `nv_encid` varchar(255) NOT NULL COMMENT '네이버 고유아이디',
  `in_division` enum('I','B') NOT NULL DEFAULT 'I' COMMENT '회원가입구분 개인회원/사업자회원 (I:개인회원, B:사업자회원 - 기본 I)',
  `in_join_ua` enum('PC','MOBILE','APP') NOT NULL DEFAULT 'PC' COMMENT '가입시 접속기기',
  `in_auth` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '승인여부',
  `in_auth_date` datetime DEFAULT NULL COMMENT '승인&미승인 최종 변경날짜',
  `in_mgsuid` int(11) NOT NULL DEFAULT 1 COMMENT '그룹의 고유번호',
  `in_mgsuid_old` int(11) NOT NULL COMMENT '이전 주문등급',
  `in_mgsdate` datetime DEFAULT NULL COMMENT '회원등급최근갱신일',
  `in_out_type` varchar(10) DEFAULT NULL COMMENT '탈퇴유형 (member,admin) - 회원,관리자',
  `ap_join` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '애플로그인 : 가입여부()',
  `ap_encid` varchar(255) DEFAULT NULL COMMENT '애플로그인 :: 아이디 생성 고유번호',
  `go_join` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '구글 가입',
  `go_encid` varchar(255) NOT NULL COMMENT '구글 고유아이디',
  PRIMARY KEY (`in_id`),
  KEY `in_emailsend` (`in_emailsend`),
  KEY `in_smssend` (`in_smssend`),
  KEY `in_out` (`in_out`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='회원테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_individual`
--

LOCK TABLES `smart_individual` WRITE;
/*!40000 ALTER TABLE `smart_individual` DISABLE KEYS */;
INSERT INTO `smart_individual` VALUES ('master','*4999DBFBDAA3384212A7934E8106780D2386C983','관리자','','2023-03-01','F','help@domain.com','','010-1111-1111','','','61954','광주 서구 치평동 1132-34','2','광주 서구 2순환로 2275 (치평동, 유덕I.C보조요금소)','N','N','2023-03-10 15:54:34','2023-03-29 20:30:28','2023-09-19 10:23:19','N','0000-00-00 00:00:00',NULL,314661,'9','N','N','2023-09-14 16:08:13','01','조아라','1234123-123123-31231','2023-03-09 11:45:49','N','0000-00-00 00:00:00','N','N','','N','','N','','I','PC','Y','2014-05-03 21:02:38',1,1,'2024-02-27 09:53:54','member','N',NULL,'N','');
/*!40000 ALTER TABLE `smart_individual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_individual_auth_log`
--

DROP TABLE IF EXISTS `smart_individual_auth_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_individual_auth_log` (
  `inl_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `inl_ordr_idxx` varchar(30) NOT NULL COMMENT '본인인증주문번호',
  `inl_site_cd` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '사이트 코드',
  `inl_cert_no` varchar(30) NOT NULL COMMENT '인증 번호',
  `inl_enc_cert_data` text NOT NULL COMMENT '본인인증 회원정보-암호화',
  `inl_rdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`inl_uid`),
  UNIQUE KEY `inl_ordr_idxx` (`inl_ordr_idxx`),
  KEY `inl_ordr_idxx_2` (`inl_ordr_idxx`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='kcp 본인인증 로그관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_individual_auth_log`
--

LOCK TABLES `smart_individual_auth_log` WRITE;
/*!40000 ALTER TABLE `smart_individual_auth_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_individual_auth_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_individual_block`
--

DROP TABLE IF EXISTS `smart_individual_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_individual_block` (
  `ib_uid` int(11) NOT NULL AUTO_INCREMENT,
  `ib_inid` varchar(50) DEFAULT NULL,
  `ib_block_inid` varchar(50) DEFAULT NULL,
  `ib_type` varchar(20) DEFAULT NULL,
  `ib_rdate` datetime DEFAULT NULL,
  PRIMARY KEY (`ib_uid`) USING BTREE,
  KEY `ib_type` (`ib_type`) USING BTREE,
  KEY `ib_block_inid` (`ib_block_inid`) USING BTREE,
  KEY `ib_inid` (`ib_inid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='회원 차단 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_individual_block`
--

LOCK TABLES `smart_individual_block` WRITE;
/*!40000 ALTER TABLE `smart_individual_block` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_individual_block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_individual_coupon`
--

DROP TABLE IF EXISTS `smart_individual_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_individual_coupon` (
  `coup_uid` varchar(20) NOT NULL,
  `coup_inid` varchar(50) NOT NULL COMMENT '등록한 개인회원',
  `coup_price` int(10) DEFAULT 0 COMMENT '쿠폰 할인/적립 제공 원@주문에서 업데이트',
  `coup_type` varchar(50) NOT NULL COMMENT '쿠폰의 혜택유형(ocs_boon_type 값과 같다. - ''discount'', ''save'', ''delivery'')@주문에서 업데이트',
  `coup_use` enum('Y','N','W','E') DEFAULT 'N' COMMENT '쿠폰사용여부 W: 사용대기, E-만료',
  `coup_usedate` datetime DEFAULT NULL COMMENT '사용일',
  `coup_rdate` datetime DEFAULT NULL COMMENT '등록일',
  `coup_ocs_uid` int(11) NOT NULL COMMENT '쿠폰셋 UID',
  `coup_expdate` date NOT NULL COMMENT '만료일',
  `coup_ocsinfo` text DEFAULT NULL COMMENT '쿠폰발급당시 해당쿠폰 내용 serialize 화',
  `coup_checksum` varchar(30) DEFAULT NULL COMMENT '특수값 저장 (ex. 자동쿠폰 첫주문,구매 일경우 주문번호, 상품코드 등.)',
  PRIMARY KEY (`coup_uid`),
  KEY `coup_use` (`coup_use`),
  KEY `coup_inid` (`coup_inid`),
  KEY `coup_ocs_uid` (`coup_ocs_uid`),
  KEY `coup_checksum` (`coup_checksum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='개인지급 쿠폰관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_individual_coupon`
--

LOCK TABLES `smart_individual_coupon` WRITE;
/*!40000 ALTER TABLE `smart_individual_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_individual_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_individual_coupon_form`
--

DROP TABLE IF EXISTS `smart_individual_coupon_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_individual_coupon_form` (
  `ocf_uid` int(11) NOT NULL AUTO_INCREMENT,
  `ocf_cookie` varchar(50) NOT NULL COMMENT '(쿠키아이디) (50)',
  `ocf_coupuid` varchar(20) NOT NULL COMMENT '(쿠폰고유번호) (20)',
  `ocf_rdate` datetime NOT NULL COMMENT '(기록일)',
  PRIMARY KEY (`ocf_uid`),
  KEY `ocf_cookie` (`ocf_cookie`),
  KEY `ocf_coupuid` (`ocf_coupuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='주문작성페이지에서 쿠폰 적용에 따른 데이터테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_individual_coupon_form`
--

LOCK TABLES `smart_individual_coupon_form` WRITE;
/*!40000 ALTER TABLE `smart_individual_coupon_form` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_individual_coupon_form` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_individual_coupon_set`
--

DROP TABLE IF EXISTS `smart_individual_coupon_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_individual_coupon_set` (
  `ocs_uid` int(11) NOT NULL AUTO_INCREMENT,
  `ocs_name` varchar(30) NOT NULL COMMENT '쿠폰명',
  `ocs_type` enum('order','delivery') NOT NULL DEFAULT 'order' COMMENT '쿠폰유형 (''order'',''delivery'') 상품쿠폰,주문쿠폰,배송쿠폰 - product ',
  `ocs_dtype` enum('price','per') NOT NULL DEFAULT 'price' COMMENT '쿠폰 금액타입 (''price'',''per'') 금액,원',
  `ocs_price_max` int(11) NOT NULL COMMENT ' per 일경우 최대 price 할인,적립 금액',
  `ocs_price_max_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'per 일경우 최대 price를 사용할지 체크여부',
  `ocs_pcode` varchar(20) DEFAULT NULL COMMENT '상품코드',
  `ocs_price` int(11) NOT NULL COMMENT '쿠폰할인금액 또는 적립금액',
  `ocs_per` decimal(3,1) NOT NULL COMMENT '쿠폰할인 또는 적립률',
  `ocs_cur` enum('KR','US') NOT NULL DEFAULT 'KR' COMMENT '미사용',
  `ocs_limit` int(11) NOT NULL COMMENT '쿠폰 구매시 최소구매금액',
  `ocs_expire` int(5) NOT NULL DEFAULT 0 COMMENT '쿠폰유효기간 ocs_use_date_type 이 day 일경우',
  `ocs_view` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '발급여부 (''Y'',''N'') - 발급, 발급안함 - N  ',
  `ocs_rdate` datetime NOT NULL COMMENT '쿠폰등록일',
  `ocs_sdate` date NOT NULL COMMENT '쿠폰발급시작일 ocs_use_date_type 이 date 일경우',
  `ocs_edate` date NOT NULL COMMENT '쿠폰발급종료일 ocs_use_date_type 이 date 일경우',
  `ocs_issued_type` enum('manual','auto') NOT NULL DEFAULT 'manual' COMMENT '발급방법 (''manual'',''auto'') 수동발급, 자동발급 ',
  `ocs_issued_type_auto` varchar(4) NOT NULL COMMENT '발급방법이 자동발급일경우 자동발급유형 (''1'',''2'',''3'',''4'') 첫 구매/결제완료, 구매/결제완료, 생일축하, 회원가입',
  `ocs_use_date_type` enum('date','expire') NOT NULL DEFAULT 'date' COMMENT '사용기간설정  (''date'',''expire'') 사용기간 지정, 사용가능일 지정',
  `ocs_boon_type` enum('discount','save','delivery') NOT NULL DEFAULT 'discount' COMMENT '쿠폰혜택 (''discount'',''save'',''delivery'') - 구매시 할인, 구매시 적립, 배송비할인',
  `ocs_issued_cnt_type` enum('limit','cnt') NOT NULL DEFAULT 'limit' COMMENT '발급수량유형 - (''limit'',''cnt'') - 제한없음,수량제한',
  `ocs_issued_cnt` int(11) NOT NULL COMMENT 'ocs_issued_cnt_type cnt 일경우 수량값',
  `ocs_issued_due_type` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '같은쿠폰을 재발급 할지 여부(Y: 중복발급함, N: 중복발급하지 않음)',
  `ocs_issued_group` text NOT NULL COMMENT '쿠폰 발급 가능한 회원등급 (text 로 되어있음, 각 그룹의 고유번호를 저장)',
  `ocs_due_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '같은유형의 쿠폰과 중복사용 가능여부 (''Y'',''N'') - 중복사용가능, 중복사용 불가 ',
  `ocs_desc` varchar(255) DEFAULT NULL COMMENT '쿠폰 설명',
  `ocs_log` text DEFAULT NULL COMMENT '쿠폰로그기록  serialize 화 ',
  PRIMARY KEY (`ocs_uid`),
  KEY `ocs_type` (`ocs_type`),
  KEY `ocs_view` (`ocs_view`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='쿠폰설정관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_individual_coupon_set`
--

LOCK TABLES `smart_individual_coupon_set` WRITE;
/*!40000 ALTER TABLE `smart_individual_coupon_set` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_individual_coupon_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_individual_payple_info`
--

DROP TABLE IF EXISTS `smart_individual_payple_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_individual_payple_info` (
  `ipi_inid` varchar(30) NOT NULL COMMENT '회원아이디',
  `ipi_payer_id` varchar(100) DEFAULT NULL COMMENT '빌링키',
  `ipi_rdate` datetime NOT NULL COMMENT '등록일',
  UNIQUE KEY `ipi_inid` (`ipi_inid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='페이플 간편결제 연동정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_individual_payple_info`
--

LOCK TABLES `smart_individual_payple_info` WRITE;
/*!40000 ALTER TABLE `smart_individual_payple_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_individual_payple_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_individual_sleep`
--

DROP TABLE IF EXISTS `smart_individual_sleep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_individual_sleep` (
  `ins_rdate` datetime NOT NULL COMMENT '휴면계정 전환일',
  `ins_mailing` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '휴면계정 메일발송 여부',
  `in_id` varchar(30) NOT NULL DEFAULT '',
  `in_pw` varchar(50) NOT NULL COMMENT '비밀번호',
  `in_name` varchar(30) NOT NULL COMMENT '이름',
  `in_staff` char(10) NOT NULL DEFAULT '' COMMENT 'md : MD, NULL : 일반회원',
  `in_birth` date NOT NULL COMMENT '생년월일',
  `in_sex` enum('M','F','') NOT NULL DEFAULT '' COMMENT '성별',
  `in_email` varchar(50) NOT NULL COMMENT '이메일',
  `in_tel` varchar(20) DEFAULT NULL COMMENT '전화',
  `in_tel2` varchar(20) NOT NULL COMMENT '핸드폰',
  `in_zip1` char(3) NOT NULL DEFAULT '' COMMENT '우편번호1',
  `in_zip2` char(3) NOT NULL DEFAULT '' COMMENT '우편번호2',
  `in_zonecode` varchar(10) NOT NULL COMMENT '국가기초구역번호',
  `in_address1` varchar(255) NOT NULL DEFAULT '' COMMENT '주소1',
  `in_address2` varchar(255) NOT NULL DEFAULT '' COMMENT '주소2',
  `in_address_doro` varchar(255) NOT NULL COMMENT '도로명주소',
  `in_emailsend` enum('Y','N') DEFAULT 'Y' COMMENT '이메일 수신여부',
  `in_smssend` enum('Y','N') DEFAULT 'Y' COMMENT '문자 수신여부',
  `in_rdate` datetime DEFAULT NULL COMMENT '가입일',
  `in_mdate` datetime DEFAULT NULL COMMENT '수정일',
  `in_ldate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '마지막 로그인',
  `in_out` enum('Y','N') DEFAULT 'N' COMMENT '탈퇴여부 - Y 일경우 탈퇴',
  `in_odate` datetime NOT NULL COMMENT '회원 탈퇴일',
  `in_ip` varchar(20) DEFAULT NULL,
  `in_point` int(10) NOT NULL DEFAULT 0 COMMENT '회원 소유 포인트',
  `in_userlevel` char(2) NOT NULL DEFAULT '2' COMMENT '회원레벨 - 일반회원:2 , 관리자:9',
  `in_sleep_type` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '휴면전환타입',
  `in_pw_rdate` datetime NOT NULL,
  `in_cancel_bank` varchar(100) NOT NULL COMMENT '환불은행',
  `in_cancel_bank_name` varchar(100) NOT NULL COMMENT '환불예금주',
  `in_cancel_bank_account` varchar(100) NOT NULL COMMENT '환불계좌번호',
  `m_opt_date` datetime NOT NULL COMMENT '수신동의/거부일자',
  `sns_join` enum('Y','N') NOT NULL DEFAULT 'N',
  `fb_join` enum('Y','N') NOT NULL DEFAULT 'N',
  `fb_encid` varchar(255) NOT NULL,
  `ko_join` enum('Y','N') NOT NULL DEFAULT 'N',
  `ko_encid` varchar(255) NOT NULL,
  `nv_join` enum('Y','N') NOT NULL DEFAULT 'N',
  `nv_encid` varchar(255) NOT NULL,
  `auth_use` enum('Y','N') NOT NULL DEFAULT 'N',
  `auth_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `in_division` enum('I','B') NOT NULL DEFAULT 'I',
  `in_join_ua` enum('PC','MOBILE') NOT NULL DEFAULT 'PC',
  `in_auth` enum('Y','N') NOT NULL DEFAULT 'Y',
  `in_auth_date` datetime DEFAULT NULL,
  `in_mgsuid` int(11) NOT NULL,
  `in_mgsdate` datetime DEFAULT NULL,
  `in_mgsuid_old` int(11) NOT NULL,
  `in_out_type` enum('member','admin') NOT NULL DEFAULT 'member',
  `in_sleep_request` enum('Y','N') NOT NULL DEFAULT 'N',
  `ap_join` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '애플로그인 : 가입여부()',
  `ap_encid` varchar(255) DEFAULT NULL COMMENT '애플로그인 :: 아이디 생성 고유번호',
  `go_join` enum('Y','N') NOT NULL DEFAULT 'N',
  `go_encid` varchar(255) NOT NULL,
  PRIMARY KEY (`in_id`),
  KEY `in_emailsend` (`in_emailsend`),
  KEY `in_smssend` (`in_smssend`),
  KEY `in_out` (`in_out`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='휴면회원테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_individual_sleep`
--

LOCK TABLES `smart_individual_sleep` WRITE;
/*!40000 ALTER TABLE `smart_individual_sleep` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_individual_sleep` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_loginchk`
--

DROP TABLE IF EXISTS `smart_loginchk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_loginchk` (
  `lc_uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lc_mid` varchar(50) NOT NULL DEFAULT '' COMMENT '로그인 아이디',
  `lc_type` enum('company','individual','deny') NOT NULL DEFAULT 'company' COMMENT '저장 타입',
  `lc_ip` varchar(15) NOT NULL DEFAULT '0' COMMENT '로그인 아이피',
  `lc_rdate` datetime DEFAULT NULL COMMENT '저장일자',
  PRIMARY KEY (`lc_uid`),
  KEY `lc_mid` (`lc_mid`),
  KEY `lc_type` (`lc_type`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='회원 로그인 로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_loginchk`
--

LOCK TABLES `smart_loginchk` WRITE;
/*!40000 ALTER TABLE `smart_loginchk` DISABLE KEYS */;
INSERT INTO `smart_loginchk` VALUES (1,'master','individual','112.219.125.10','2023-06-09 14:05:39'),(2,'master','individual','112.219.125.10','2023-09-14 16:08:13'),(3,'master','individual','112.219.125.10','2023-09-19 10:23:19');
/*!40000 ALTER TABLE `smart_loginchk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_mailing_data`
--

DROP TABLE IF EXISTS `smart_mailing_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_mailing_data` (
  `md_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `md_title` varchar(100) DEFAULT NULL COMMENT '메일링 제목',
  `md_content` text DEFAULT NULL COMMENT '메일링 내용',
  `md_rdate` datetime DEFAULT NULL COMMENT '메일링 등록일시',
  `md_adchk` enum('Y','N') NOT NULL COMMENT '광고성, 이벤트성 메일링 체크 유무',
  PRIMARY KEY (`md_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='메일링정보관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_mailing_data`
--

LOCK TABLES `smart_mailing_data` WRITE;
/*!40000 ALTER TABLE `smart_mailing_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_mailing_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_mailing_profile`
--

DROP TABLE IF EXISTS `smart_mailing_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_mailing_profile` (
  `mp_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_mduid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '메일링정보(smart_mailing_data) 고유번호',
  `mp_brother` varchar(50) NOT NULL DEFAULT '' COMMENT '일괄발송 횟수 구분을 위한 키값 메일 일괄 발송시 같은 값을 가지며, 같은 메일을 다시 발송할 경우 다른 값을 가지고 발송횟수도 증가함',
  `mp_email` text DEFAULT NULL COMMENT '이메일. 콤마(,) 구분으로 한번에 발송할 데이터 저장',
  `mp_status` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '발송상태',
  `mp_rdate` datetime DEFAULT NULL COMMENT '메일링 기록일시',
  `mp_sdate` datetime DEFAULT NULL COMMENT '메일링 발송일시',
  PRIMARY KEY (`mp_uid`),
  KEY `mp_mduid` (`mp_mduid`),
  KEY `mp_status` (`mp_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='메일링회원정보관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_mailing_profile`
--

LOCK TABLES `smart_mailing_profile` WRITE;
/*!40000 ALTER TABLE `smart_mailing_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_mailing_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_member_080_deny`
--

DROP TABLE IF EXISTS `smart_member_080_deny`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_member_080_deny` (
  `md_uid` int(10) NOT NULL AUTO_INCREMENT,
  `md_refusal_num` varchar(100) DEFAULT NULL COMMENT '080 수신거부 번호',
  `md_refusal_time` varchar(50) DEFAULT NULL COMMENT '수신거부 요청 시간',
  `md_hp` varchar(50) DEFAULT NULL COMMENT '수신거부 요청 전화번호',
  `md_status` enum('OK','MULTI','NO','FALSE') DEFAULT 'OK' COMMENT '처리상태 - OK : 정상거부처리 , MULTI : 다수검색으로 인한 미처리 , NO : 미검색으로 인한 미처리 , FALSE : 080 수신거부 관리자 미설정 오류',
  `md_rdate` datetime DEFAULT NULL COMMENT '저장일시',
  PRIMARY KEY (`md_uid`),
  KEY `md_status` (`md_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='080 수신거부 기록';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_member_080_deny`
--

LOCK TABLES `smart_member_080_deny` WRITE;
/*!40000 ALTER TABLE `smart_member_080_deny` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_member_080_deny` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_member_group_set`
--

DROP TABLE IF EXISTS `smart_member_group_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_member_group_set` (
  `mgs_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '등급 고유번호(회원에 mgsuid 로 저장)',
  `mgs_rank` int(11) NOT NULL COMMENT '등급 순위(높을수록 높은등급)',
  `mgs_idx` int(11) NOT NULL DEFAULT 1 COMMENT '등급노출순서',
  `mgs_name` varchar(50) NOT NULL COMMENT '등급명(직접입력한 등급명)',
  `mgs_icon` varchar(200) NOT NULL COMMENT '등급별아이콘(PC)',
  `mgs_mobile_icon` varchar(200) NOT NULL COMMENT '등급별아이콘(MOBILE)',
  `mgs_condition_totprice` int(11) NOT NULL COMMENT '등급조건 주문금액',
  `mgs_condition_totcnt` int(11) NOT NULL COMMENT '등급조건 : 구매회수',
  `mgs_give_point_per` float(10,1) NOT NULL COMMENT '등급 혜택 : 적립포인트',
  `mgs_sale_price_per` float(10,1) NOT NULL COMMENT '등급 혜택 : 할인포인트',
  `mgs_rdate` datetime NOT NULL COMMENT '등급등록일',
  PRIMARY KEY (`mgs_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='회원 그룹 설정 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_member_group_set`
--

LOCK TABLES `smart_member_group_set` WRITE;
/*!40000 ALTER TABLE `smart_member_group_set` DISABLE KEYS */;
INSERT INTO `smart_member_group_set` VALUES (1,1,1,'Welcome','698959952.png','2582752989.png',0,0,0.0,0.0,'2023-03-15 10:19:39'),(8,4,4,'Silver','','1328681833.png',136000,2,0.1,0.1,'2023-03-14 15:29:10'),(5,3,3,'Gold','2245521052.png','355330975.png',20000,50,0.1,5.5,'2023-03-15 23:19:16'),(10,5,2,'VIP','','',5000,5,0.0,1.0,'2023-03-29 15:01:22');
/*!40000 ALTER TABLE `smart_member_group_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_normal_page`
--

DROP TABLE IF EXISTS `smart_normal_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_normal_page` (
  `np_uid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `np_rdate` datetime NOT NULL COMMENT '페이지등록일',
  `np_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '노출여부',
  `np_id` varchar(30) DEFAULT NULL COMMENT '페이지아이디',
  `np_title` varchar(200) DEFAULT NULL COMMENT '페이지명',
  `np_content` text DEFAULT NULL COMMENT '페이지내용',
  `np_content_m` text NOT NULL COMMENT '모바일 내용',
  `np_header_img` varchar(255) NOT NULL COMMENT '일반페이지 상단이미지',
  `np_footer_img` varchar(255) NOT NULL COMMENT '일반페이지 하단이미지',
  `np_idx` int(10) NOT NULL DEFAULT 0 COMMENT '페이지순서 - 낮은순으로 먼저 나옴',
  `np_use_content` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'PC, 모바일 상세내용 함께사용 여부',
  `np_menu` varchar(30) NOT NULL DEFAULT 'default' COMMENT '노출메뉴',
  PRIMARY KEY (`np_uid`),
  KEY `np_view` (`np_view`),
  KEY `np_menu` (`np_menu`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='일반페이지관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_normal_page`
--

LOCK TABLES `smart_normal_page` WRITE;
/*!40000 ALTER TABLE `smart_normal_page` DISABLE KEYS */;
INSERT INTO `smart_normal_page` VALUES (3,'2023-03-28 13:11:15','Y','company','회사소개','<p align=\"center\" style=\"text-align: center;\"><img src=\"/upfiles/smarteditor/2023032011122071211653.jpg\" title=\"\"><br style=\"clear:both;\">&nbsp;</p><p align=\"center\" style=\"text-align: center;\"><span style=\"font-size: 12pt;\"><br style=\"clear:both;\">&nbsp;</span></p>','','','',1,'N','default'),(5,'2023-03-28 11:14:58','Y','location','서비스안내','<div style=\"text-align: center;\" align=\"center\"><img src=\"/upfiles/smarteditor/202303201111511176330953.jpg\" title=\"\"></div>','','','',4,'N','default');
/*!40000 ALTER TABLE `smart_normal_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_npay`
--

DROP TABLE IF EXISTS `smart_npay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_npay` (
  `c_uid` int(10) NOT NULL AUTO_INCREMENT,
  `c_cookie` varchar(50) NOT NULL DEFAULT '' COMMENT '접속COOKIE코드',
  `c_pcode` varchar(20) NOT NULL DEFAULT '' COMMENT '상품고유코드',
  `c_pouid` int(10) unsigned NOT NULL COMMENT '옵션고유번호 - 3차일 경우 3차 옵션 uid',
  `c_option1` varchar(100) DEFAULT NULL COMMENT '선택 옵션1',
  `c_option2` varchar(100) DEFAULT NULL COMMENT '선택 옵션2',
  `c_option3` varchar(100) DEFAULT NULL COMMENT '선택 옵션3',
  `c_cnt` int(5) DEFAULT NULL COMMENT '수량',
  `c_supply_price` int(10) NOT NULL DEFAULT 0 COMMENT '공급가',
  `c_price` int(10) NOT NULL DEFAULT 0 COMMENT '할인가 - 실판매가',
  `c_point` float NOT NULL DEFAULT 0 COMMENT '적용포인트량',
  `c_rdate` datetime DEFAULT NULL COMMENT '등록일시',
  `c_direct` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '바로구매 (Y:바로구매,N:장바구니구매)',
  `c_is_addoption` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '추가옵션여부',
  `c_addoption_parent` int(11) NOT NULL COMMENT '추가옵션일때 필수옵션 uid',
  `c_uniq` varchar(255) NOT NULL COMMENT 'Npay주문등록 고유키',
  PRIMARY KEY (`c_uid`),
  KEY `c_cookie` (`c_cookie`),
  KEY `c_pcode` (`c_pcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='네이버페이 주문관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_npay`
--

LOCK TABLES `smart_npay` WRITE;
/*!40000 ALTER TABLE `smart_npay` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_npay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_online_notice`
--

DROP TABLE IF EXISTS `smart_online_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_online_notice` (
  `on_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `on_view` enum('Y','N') NOT NULL COMMENT '노출여부',
  `on_name` varchar(30) NOT NULL COMMENT '입금자명',
  `on_price` int(11) NOT NULL DEFAULT 0 COMMENT '입금액',
  `on_bank` varchar(255) NOT NULL COMMENT '입금은행',
  `on_date` date NOT NULL COMMENT '입금일자',
  `on_obuid` int(11) NOT NULL COMMENT '실시간입금확인 고유번호',
  `on_rdate` datetime NOT NULL COMMENT '등록일시',
  PRIMARY KEY (`on_uid`),
  KEY `on_view` (`on_view`,`on_date`),
  KEY `on_obuid` (`on_obuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='미확인 입금자 확인 목록';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_online_notice`
--

LOCK TABLES `smart_online_notice` WRITE;
/*!40000 ALTER TABLE `smart_online_notice` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_online_notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order`
--

DROP TABLE IF EXISTS `smart_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order` (
  `o_ordernum` varchar(20) NOT NULL DEFAULT '' COMMENT '주문번호 - 숫자조합으로 15글자 적용, 예)12545-12345-12365',
  `o_memtype` enum('Y','N') DEFAULT 'Y' COMMENT '회원타입, Y:회원, N:비회원',
  `o_mid` varchar(50) DEFAULT NULL COMMENT '주문자 아이디, 비회원일 경우 장바구니 c_cookie정보 저장',
  `o_oname` varchar(30) DEFAULT NULL COMMENT '주문자명',
  `o_otel` varchar(30) DEFAULT NULL COMMENT '주문자-연락처',
  `o_ohp` varchar(30) NOT NULL COMMENT '주문자 핸드폰',
  `o_oemail` varchar(100) DEFAULT NULL COMMENT '주문자-이메일',
  `o_rname` varchar(30) DEFAULT NULL COMMENT '수신자명',
  `o_rtel` varchar(30) DEFAULT NULL COMMENT '수신자-연락처',
  `o_rhp` varchar(30) NOT NULL COMMENT '수신자 핸드폰',
  `o_rpost` varchar(7) DEFAULT NULL COMMENT '수신자-우편번호 , 예) 123-567',
  `o_rzonecode` varchar(10) NOT NULL COMMENT '국가기초구역번호',
  `o_raddr1` varchar(100) DEFAULT NULL COMMENT '수신자-주소1',
  `o_raddr2` varchar(100) DEFAULT NULL COMMENT '수신자-주소2',
  `o_raddr_doro` varchar(255) DEFAULT NULL COMMENT '도로명주소',
  `o_content` mediumtext DEFAULT NULL COMMENT '배송시 문구',
  `o_uname` varchar(30) DEFAULT NULL COMMENT '사용자명',
  `o_uhp` varchar(30) DEFAULT NULL COMMENT '사용자-휴대폰',
  `o_uemail` varchar(100) DEFAULT NULL COMMENT '사용자-이메일',
  `o_admcontent` mediumtext DEFAULT NULL COMMENT '관리자메모',
  `o_price_real` int(10) DEFAULT NULL COMMENT '실결제가',
  `o_price_total` int(10) DEFAULT NULL COMMENT '구매총액',
  `o_price_delivery` int(10) DEFAULT NULL COMMENT '배송액',
  `o_price_supplypoint` int(10) DEFAULT NULL COMMENT '포인트제공액, 주문에 따라 제공해야함',
  `o_price_usepoint` int(10) NOT NULL DEFAULT 0 COMMENT '포인트사용액',
  `o_apply_point` enum('Y','N') DEFAULT 'N' COMMENT '결제에 따른 포인트, 쿠폰등의 가감적용',
  `o_price_coupon_individual` int(10) NOT NULL DEFAULT 0 COMMENT '보너스쿠폰사용액',
  `o_save_price_coupon_individual` int(11) NOT NULL COMMENT '회원쿠폰 적립금액 총액',
  `o_coupon_individual_uid` text DEFAULT NULL COMMENT '사용자 쿠폰 uid, 콤마로 분리',
  `o_price_coupon_product` int(10) NOT NULL DEFAULT 0 COMMENT '상품쿠폰사용액',
  `o_paymethod` enum('card','iche','online','point','virtual','hpp','payco','payple') DEFAULT 'card' COMMENT '결제방식, payple 추가',
  `o_paystatus` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '결제여부 - Y:결제확인, N:미결제',
  `o_canceled` enum('Y','N','R') DEFAULT 'N' COMMENT '결제취소상태, Y:결제취소확인, N:결제취소안됨, R:환불요청',
  `o_status` varchar(15) DEFAULT NULL COMMENT '주문상태, 배송중, 결제대기,결제완료,배송진행,배송완료,발급완료,주문취소,환불처리',
  `o_bank` varchar(50) DEFAULT NULL COMMENT '무통장입금시 - 입금 계좌정보',
  `o_get_tax` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '현금영수증 발행여부',
  `o_deposit` varchar(50) DEFAULT NULL COMMENT '무통장입금시 - 입금자명',
  `o_rdate` datetime DEFAULT NULL COMMENT '주문일시',
  `o_web_mode` varchar(20) NOT NULL COMMENT '사용자 웹 환경 pc , mobile',
  `o_sendcompany` varchar(30) DEFAULT NULL COMMENT '택배사',
  `o_sendnum` varchar(20) DEFAULT NULL COMMENT '송장번호',
  `o_sendstatus` varchar(50) DEFAULT NULL COMMENT '배송대기,배송진행,배송완료,발급완료',
  `o_senddate` date DEFAULT NULL COMMENT '배송일',
  `o_completedate` date NOT NULL DEFAULT '0000-00-00' COMMENT '배송/발급 완료일(구매확정일)',
  `o_moneyback` varchar(20) NOT NULL COMMENT '환불요청',
  `o_moneyback_status` enum('none','request','complete') NOT NULL DEFAULT 'none' COMMENT '환불상태 - none : 미신청 , request : 환불신청 , complete : 환불처리완료',
  `o_moneyback_comment` varchar(200) NOT NULL,
  `o_moneyback_date` datetime NOT NULL,
  `o_moneyback_comdate` datetime NOT NULL COMMENT '환불완료일',
  `o_promotion_code` varchar(200) NOT NULL COMMENT '적용된 프로모션 코드',
  `o_promotion_price` int(11) NOT NULL DEFAULT 0 COMMENT '적용된 프로모션코드 할인금액',
  `o_paystatus3` enum('none','ready','complete') NOT NULL DEFAULT 'none' COMMENT '주문정산상태 - none : 정산무관, ready : 정산대기, complete : 정산완료',
  `o_price_refund` int(10) NOT NULL DEFAULT 0 COMMENT '부분취소시 환불/취소한 금액',
  `o_price_usepoint_refund` int(11) NOT NULL DEFAULT 0 COMMENT '부분취소시 환불한 포인트',
  `npay_order` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'NPay로 구매여부',
  `npay_uniq` varchar(255) NOT NULL COMMENT 'NPay 구매 고유아이디',
  `mobile` enum('Y','N','A') NOT NULL DEFAULT 'N' COMMENT '모바일 구매 여부 (Y:모바일구매,N:PC구매,A:앱구매)',
  `npay_status` varchar(255) NOT NULL COMMENT 'NPay 상태 전달값',
  `npay_order_group` varchar(255) NOT NULL COMMENT '네이버 주문코드',
  `o_area` varchar(10) NOT NULL COMMENT '주문지역 - 통계용',
  `o_tax_TradeUsage` char(1) NOT NULL COMMENT '현금영수증거래용도 ',
  `o_tax_TradeMethod` char(1) NOT NULL COMMENT '현금영수증 신분확인번호 구분 - 1: 카드변호, 3:주민등록번호, 4:사업자번호, 5:휴대폰번호 (구-2017-01-13일이전정보 / 현금영수증 신분확인번호 구분 - 1: 카드변호, 2:주민등록번호, 3:사업자번호, 4:휴대폰번호)',
  `o_tax_IdentityNum` varchar(100) NOT NULL COMMENT '신분확인번호',
  `o_tax_error` varchar(255) NOT NULL COMMENT '현금영수증발행시오류메세지',
  `o_paydate` datetime NOT NULL COMMENT '결제완료일시',
  `o_canceldate` datetime NOT NULL COMMENT '주문취소일시',
  `device_info` text NOT NULL COMMENT '주문기기정보',
  `payco_paymethod_code` varchar(10) NOT NULL COMMENT '페이코 결제수단 코드',
  `payco_paymethod_name` varchar(30) NOT NULL COMMENT '페이코 결제수단 이름',
  `o_cancel_mem_type` enum('member','admin') NOT NULL DEFAULT 'member' COMMENT '부분취소요청구분(회원취소, 관리자취소)',
  `o_paycancel_method` enum('F','B','D') DEFAULT 'D' COMMENT '결제 취소시 포인트 환불 방식',
  `o_price_discount_refund` int(10) NOT NULL DEFAULT 0 COMMENT '부분취소된 할인액(환불불가, 보너스쿠폰 + 프로모션코드 + 앱주문 할인)',
  `o_price_product_refund` int(10) NOT NULL DEFAULT 0 COMMENT '부분취소된 개별할인액(상품쿠폰)',
  `o_price_to_usepoint_refund` int(10) NOT NULL DEFAULT 0 COMMENT '카드/현금 환불이지만 적립금 환불한 경우 별도 표시 o_price_refund에 포함됨',
  `o_easypay_paymethod_type` varchar(30) DEFAULT NULL COMMENT '신용카드 간편결제 수단 (var.php에 정의 - arr_available_easypay_pg )',
  `o_order_type` enum('delivery','ticket','both') DEFAULT 'delivery' COMMENT '주문타입(delivery: 배송상품 , ticket: 티켓상품, both: 배송/티켓 둘다)',
  `o_directpay_status` enum('none','ready','complete') DEFAULT 'none' COMMENT '바로결제 타입(none:상관없음 , ready:준비중, complete:완료)',
  `o_price_appsale` int(11) NOT NULL COMMENT '앱 주문시 할인액',
  PRIMARY KEY (`o_ordernum`),
  KEY `o_memtype` (`o_memtype`),
  KEY `o_mid` (`o_mid`),
  KEY `o_apply_point` (`o_apply_point`),
  KEY `o_paymethod` (`o_paymethod`),
  KEY `o_paystatus` (`o_paystatus`),
  KEY `o_status` (`o_status`),
  KEY `o_moneyback_status` (`o_moneyback_status`),
  KEY `o_area` (`o_area`),
  KEY `o_canceled` (`o_canceled`),
  KEY `npay_order` (`npay_order`),
  KEY `o_rdate` (`o_rdate`),
  KEY `o_easypay_paymethod_type` (`o_easypay_paymethod_type`),
  KEY `o_order_type` (`o_order_type`),
  KEY `o_direct_status` (`o_directpay_status`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='주문관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order`
--

LOCK TABLES `smart_order` WRITE;
/*!40000 ALTER TABLE `smart_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_cardlog`
--

DROP TABLE IF EXISTS `smart_order_cardlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_cardlog` (
  `oc_uid` int(10) NOT NULL AUTO_INCREMENT,
  `oc_oordernum` varchar(20) DEFAULT NULL COMMENT '주문번호',
  `oc_tid` varchar(100) NOT NULL DEFAULT '' COMMENT '거래번호',
  `oc_content` text DEFAULT NULL COMMENT '거래기록정보. 저장방식 LGD_CARDACQUIRER||11§§LGD_MID||tssnumer§§LGD_FINANCENAME||비씨VISA§§',
  `oc_cancle_content` text NOT NULL COMMENT '결제취소 리턴메세지',
  `oc_rdate` datetime DEFAULT NULL COMMENT '저장일시',
  PRIMARY KEY (`oc_uid`),
  KEY `oc_oordernum` (`oc_oordernum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='주문결제기록(카드/이체)관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_cardlog`
--

LOCK TABLES `smart_order_cardlog` WRITE;
/*!40000 ALTER TABLE `smart_order_cardlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_cardlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_cashlog`
--

DROP TABLE IF EXISTS `smart_order_cashlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_cashlog` (
  `ocs_uid` int(11) NOT NULL AUTO_INCREMENT,
  `ocs_ordernum` varchar(20) NOT NULL,
  `ocs_member` varchar(100) NOT NULL,
  `ocs_date` datetime NOT NULL,
  `ocs_tid` varchar(30) NOT NULL,
  `ocs_cashnum` varchar(20) NOT NULL,
  `ocs_respdate` varchar(20) NOT NULL,
  `ocs_msg` varchar(100) NOT NULL,
  `ocs_method` varchar(20) NOT NULL,
  `ocs_amount` float NOT NULL,
  `ocs_cardnum` varchar(30) NOT NULL,
  `ocs_type` varchar(20) NOT NULL,
  `ocs_seqno` varchar(10) NOT NULL,
  PRIMARY KEY (`ocs_uid`),
  KEY `ocs_type` (`ocs_type`),
  KEY `ocs_method` (`ocs_method`),
  KEY `ocs_member` (`ocs_member`),
  KEY `ocs_ordernum` (`ocs_ordernum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='현금영수증 로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_cashlog`
--

LOCK TABLES `smart_order_cashlog` WRITE;
/*!40000 ALTER TABLE `smart_order_cashlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_cashlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_company`
--

DROP TABLE IF EXISTS `smart_order_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_company` (
  `ocp_uid` int(10) NOT NULL AUTO_INCREMENT,
  `ocp_cpid` varchar(50) NOT NULL DEFAULT '' COMMENT '등록 공급업체아이디',
  `ocp_oordernum` varchar(20) NOT NULL DEFAULT '' COMMENT '주문번호 - 숫자조합으로 15글자 적용, 예)12545-12345-12365',
  `ocp_delivery_price` int(10) NOT NULL DEFAULT 0 COMMENT '배송료 합계금액',
  `ocp_product_sell_price` int(10) NOT NULL DEFAULT 0 COMMENT '상품판매가격',
  `ocp_product_sell_count` int(10) NOT NULL DEFAULT 0 COMMENT '상품판매수량',
  `ocp_commission` int(10) NOT NULL DEFAULT 0 COMMENT '수수료',
  `ocp_settle_price` int(10) NOT NULL DEFAULT 0 COMMENT '정산지급액',
  PRIMARY KEY (`ocp_uid`),
  KEY `os_oordernum` (`ocp_oordernum`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='상점 정산관련 정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_company`
--

LOCK TABLES `smart_order_company` WRITE;
/*!40000 ALTER TABLE `smart_order_company` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_coupon_log`
--

DROP TABLE IF EXISTS `smart_order_coupon_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_coupon_log` (
  `cl_uid` int(11) NOT NULL AUTO_INCREMENT,
  `cl_type` enum('member','product') NOT NULL DEFAULT 'member' COMMENT '쿠폰유형 member-사용자쿠폰, product-상품쿠폰',
  `cl_title` varchar(100) NOT NULL COMMENT '사용쿠폰 타이틀',
  `cl_price` int(11) NOT NULL DEFAULT 0 COMMENT '쿠폰금액',
  `cl_oordernum` varchar(20) NOT NULL COMMENT '쿠폰을 사용한 주문번호',
  `cl_coNo` varchar(20) NOT NULL DEFAULT '' COMMENT '(사용자쿠폰전용) 사용자쿠폰 번호',
  `cl_pcode` varchar(20) NOT NULL COMMENT '(상품쿠폰전용) 상품코드',
  `cl_rdate` datetime NOT NULL COMMENT '로그 등록일',
  `cl_cancel_status` enum('N','Y') NOT NULL DEFAULT 'N' COMMENT '부분취소상태 - product 일 경우 해당함.',
  `cl_cancel_opuid` int(10) NOT NULL COMMENT '취소 시 주문상품고유번호',
  PRIMARY KEY (`cl_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='주문시 쿠폰사용 로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_coupon_log`
--

LOCK TABLES `smart_order_coupon_log` WRITE;
/*!40000 ALTER TABLE `smart_order_coupon_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_coupon_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_onlinelog`
--

DROP TABLE IF EXISTS `smart_order_onlinelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_onlinelog` (
  `ool_uid` int(11) NOT NULL AUTO_INCREMENT,
  `ool_ordernum` varchar(20) NOT NULL COMMENT '주문번호',
  `ool_member` varchar(100) NOT NULL COMMENT '회원아이디',
  `ool_date` datetime NOT NULL COMMENT '저장일',
  `ool_tid` varchar(100) NOT NULL COMMENT '승인번호',
  `ool_type` enum('R','I','C') NOT NULL COMMENT '상태',
  `ool_respdate` varchar(20) NOT NULL COMMENT '결제일자',
  `ool_msg` varchar(100) NOT NULL COMMENT '메시지',
  `ool_status` varchar(20) NOT NULL COMMENT '미사용',
  `ool_amount_current` varchar(100) NOT NULL COMMENT '금액',
  `ool_amount_total` varchar(100) NOT NULL COMMENT '총액(금액 + 수수료 등)',
  `ool_account_num` varchar(20) NOT NULL COMMENT '계좌번호',
  `ool_account_code` varchar(10) NOT NULL COMMENT '입금순서 - 사용하지 않는 경우 많음',
  `ool_deposit_name` varchar(50) NOT NULL COMMENT '입금자명',
  `ool_deposit_tel` varchar(30) NOT NULL COMMENT '입금자연락처',
  `ool_bank_name` varchar(20) NOT NULL COMMENT '은행명',
  `ool_bank_code` varchar(20) NOT NULL COMMENT '은행코드',
  `ool_bank_owner` varchar(10) NOT NULL COMMENT '예금주명',
  `ool_escrow` enum('Y','N') NOT NULL COMMENT '에스크로 적용 여부',
  `ool_escrow_code` varchar(20) NOT NULL COMMENT '에스크로 거래고유번호',
  `ool_escrow_fee` int(10) NOT NULL DEFAULT 0 COMMENT '에스크로 수수료',
  PRIMARY KEY (`ool_uid`),
  KEY `ool_account_code` (`ool_account_code`),
  KEY `ool_status` (`ool_status`),
  KEY `ool_type` (`ool_type`),
  KEY `ool_member` (`ool_member`),
  KEY `ool_ordernum` (`ool_ordernum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='가상계좌발급로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_onlinelog`
--

LOCK TABLES `smart_order_onlinelog` WRITE;
/*!40000 ALTER TABLE `smart_order_onlinelog` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_onlinelog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_product`
--

DROP TABLE IF EXISTS `smart_order_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_product` (
  `op_uid` int(10) NOT NULL AUTO_INCREMENT,
  `op_oordernum` varchar(20) DEFAULT NULL COMMENT '주문번호',
  `op_pcode` varchar(20) NOT NULL DEFAULT '' COMMENT '주문상품코드',
  `op_pouid` varchar(20) NOT NULL DEFAULT '' COMMENT '상품 옵션 고유번호',
  `op_option1` varchar(100) DEFAULT NULL COMMENT '선택 옵션1',
  `op_option2` varchar(100) DEFAULT NULL COMMENT '선택 옵션2',
  `op_option3` varchar(100) DEFAULT NULL COMMENT '선택 옵션3',
  `op_supply_price` int(10) NOT NULL DEFAULT 0 COMMENT '공급가',
  `op_price` int(10) NOT NULL DEFAULT 0 COMMENT '주문상품가격',
  `op_point` int(10) NOT NULL DEFAULT 0 COMMENT '주문상품당 적립포인트',
  `op_cnt` int(5) DEFAULT NULL COMMENT '상품주문갯수',
  `op_add_delivery_price` int(10) NOT NULL DEFAULT 0 COMMENT '추가배송비',
  `op_rdate` datetime DEFAULT NULL COMMENT '저장일시',
  `op_paydate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '주문상품 결제일',
  `op_sendcompany` varchar(30) DEFAULT NULL COMMENT '택배사',
  `op_sendnum` varchar(20) DEFAULT NULL COMMENT '송장번호',
  `op_sendstatus` varchar(50) NOT NULL COMMENT '배송대기,배송준비,배송중,배송완료,발급완료',
  `op_senddate` date DEFAULT NULL COMMENT '배송일',
  `op_completedate` date NOT NULL DEFAULT '0000-00-00' COMMENT '배송완료일(구매확정일)',
  `op_complain` varchar(50) NOT NULL DEFAULT '' COMMENT 'NULL, 교환/반품신청, 교환/반품완료',
  `op_complain_date` datetime NOT NULL COMMENT '컴플레인 신청일',
  `op_complain_comment` text DEFAULT NULL COMMENT '컴플레인 메모',
  `op_settlementstatus` enum('none','ready','complete') NOT NULL DEFAULT 'none' COMMENT '주문상품 정산상태 - none : 정산무관 , ready : 정산대기 , complete : 정산상태',
  `op_settlement_reday` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '정산대기 전환일',
  `op_settlement_complete` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '정산완료 전환일',
  `op_is_addoption` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '추가옵션여부',
  `op_addoption_parent` int(11) NOT NULL COMMENT '추가옵션일때 필수옵션 uid',
  `op_cancel` enum('Y','N','R') NOT NULL DEFAULT 'N' COMMENT '부분취소여부(Y:취소,N:기본,R:취소접수)',
  `op_cancel_type` enum('pg','point') NOT NULL DEFAULT 'pg',
  `op_cancel_tid` varchar(100) NOT NULL COMMENT 'PG사 부분취소 tid',
  `op_cancel_returnmsg` varchar(200) NOT NULL COMMENT 'PG사 부분취소 메세지',
  `op_cancel_msg` text NOT NULL COMMENT '사용자 부분취소 메세지',
  `op_cancel_bank` varchar(100) NOT NULL COMMENT '환불계좌 은행코드',
  `op_cancel_bank_name` varchar(100) NOT NULL COMMENT '환불계좌 예금주',
  `op_cancel_bank_account` varchar(100) NOT NULL COMMENT '환불계좌 번호',
  `op_cancel_rdate` datetime NOT NULL COMMENT '취소접수일',
  `op_cancel_cdate` datetime NOT NULL COMMENT '취소완료일',
  `op_cancel_discount_price` int(10) NOT NULL DEFAULT 0 COMMENT '취소시 할인액 - (사용포인트, 상품쿠폰 제외)',
  `op_pname` varchar(200) NOT NULL COMMENT '주문상품명',
  `op_partnerCode` varchar(100) NOT NULL COMMENT '공급업체 코드',
  `op_usepoint` int(11) NOT NULL COMMENT '정산을 위한 사용포인트 나눔',
  `op_comSaleType` enum('수수료','공급가') NOT NULL DEFAULT '공급가' COMMENT '주문상품의 정산형태',
  `op_commission` float(10,2) NOT NULL DEFAULT 10.00 COMMENT '정산형태가 수수료일 경우 수수료율',
  `op_delivery_price` int(11) NOT NULL COMMENT '배송비',
  `op_delivery_type` enum('입점','개별','무료','상품별') NOT NULL DEFAULT '입점' COMMENT '상품별 배송비 타입',
  `npay_order_code` varchar(255) NOT NULL COMMENT '네이버페이 상품주문코드',
  `npay_order_group` varchar(255) NOT NULL COMMENT '네이버 주문코드',
  `npay_uniq` varchar(255) NOT NULL COMMENT 'NPay 구매 고유아이디',
  `npay_status` varchar(255) NOT NULL COMMENT 'NPay 상태 전달값',
  `npay_sync` enum('Y','R','A') NOT NULL DEFAULT 'Y' COMMENT 'NPay 연동 상태(Y: 연동, R: 연동대기, A: 후연동)',
  `npay_point` varchar(11) NOT NULL DEFAULT '0' COMMENT '네이버페이 포인트',
  `npay_point2` varchar(11) NOT NULL DEFAULT '0' COMMENT '네이버페이 적립금',
  `op_vat` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '과세여부 - Y:과세, N:면세',
  `op_is_cashbill` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '현금영수증발행여부',
  `op_free_delivery_event_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '무료배송 이벤트 적요여부',
  `op_groupset_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '회원등급할인 적용여부',
  `op_old_price` int(11) NOT NULL COMMENT '기존가격',
  `op_old_point` int(11) NOT NULL COMMENT '기존적립포인트',
  `op_groupset_price_per` decimal(3,1) NOT NULL COMMENT '할인률',
  `op_groupset_point_per` decimal(3,1) NOT NULL COMMENT '추가적립률',
  `op_cancel_mem_type` enum('member','admin') NOT NULL DEFAULT 'member' COMMENT '부분취소요청구분(회원취소, 관리자취소)',
  `op_cancel_price` int(10) NOT NULL DEFAULT 0 COMMENT '부분취소 시 금액',
  `op_cancel_usepoint` int(10) NOT NULL DEFAULT 0 COMMENT '부분취소 시 포인트반',
  `op_use_discount_price` int(10) NOT NULL DEFAULT 0 COMMENT '정산/부분취소를 위한 할인액 나눔 (사용포인트, 상품쿠폰 제외)',
  `op_use_product_coupon` int(11) NOT NULL DEFAULT 0 COMMENT '상품쿠폰 사용액',
  `op_dateoption_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '달력옵션 사용여부(Y:사용,N:미사용)',
  `op_dateoption_date` date DEFAULT NULL COMMENT '달력옵션 사용시 선택된 날짜',
  `op_ptype` enum('delivery','ticket') DEFAULT 'delivery' COMMENT '주문상품의 타입(p_type) - delivery: 배송상품 , ticket:티켓상품',
  PRIMARY KEY (`op_uid`),
  KEY `op_oordernum` (`op_oordernum`),
  KEY `op_pcode` (`op_pcode`),
  KEY `op_sendstatus` (`op_sendstatus`),
  KEY `op_vat` (`op_vat`),
  KEY `op_dateoption_use_op_dateoption_date` (`op_dateoption_use`,`op_dateoption_date`),
  KEY `op_ptype` (`op_ptype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='주문상품관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_product`
--

LOCK TABLES `smart_order_product` WRITE;
/*!40000 ALTER TABLE `smart_order_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_product_ticket`
--

DROP TABLE IF EXISTS `smart_order_product_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_product_ticket` (
  `opt_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `opt_oordernum` varchar(20) DEFAULT NULL COMMENT '주문번호',
  `opt_pcode` varchar(20) DEFAULT NULL COMMENT '상품코드',
  `opt_opuid` int(10) unsigned NOT NULL COMMENT '주문상품의 고유번호',
  `opt_ticketnum` varchar(100) DEFAULT NULL COMMENT '쿠폰번호',
  `opt_rdatetime` datetime DEFAULT NULL COMMENT '쿠폰생성일',
  `opt_udatetime` datetime DEFAULT NULL COMMENT '쿠폰사용일',
  `opt_cdatetime` datetime DEFAULT NULL COMMENT '티켓취소일',
  `opt_expire_date` date DEFAULT NULL COMMENT '쿠폰유효기간',
  `opt_expire_type` enum('','day','date') DEFAULT NULL COMMENT '저장당시 유효기간 타입(기본없음,day:만료일, date:만료날짜)',
  `opt_status` enum('대기','사용','취소','만료') DEFAULT '대기' COMMENT '쿠폰사용상태',
  `opt_send` int(11) NOT NULL DEFAULT 0 COMMENT '쿠폰문자 재발송 카운트',
  `opt_log` text DEFAULT NULL COMMENT '처리로그',
  `opt_update_check` enum('Y','N') DEFAULT 'N' COMMENT '업데이트 체크',
  PRIMARY KEY (`opt_uid`) USING BTREE,
  KEY `opc_oordernum` (`opt_oordernum`) USING BTREE,
  KEY `opc_opuid` (`opt_opuid`) USING BTREE,
  KEY `opc_status` (`opt_status`) USING BTREE,
  KEY `opt_ticketnum` (`opt_ticketnum`),
  KEY `opt_pcode` (`opt_pcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='주문상품티켓관리 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_product_ticket`
--

LOCK TABLES `smart_order_product_ticket` WRITE;
/*!40000 ALTER TABLE `smart_order_product_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_product_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_settle`
--

DROP TABLE IF EXISTS `smart_order_settle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_settle` (
  `os_uid` int(10) NOT NULL AUTO_INCREMENT,
  `os_cpid` varchar(50) NOT NULL DEFAULT '' COMMENT '등록 기업회원아이디',
  `os_oordernum` varchar(20) NOT NULL DEFAULT '' COMMENT '주문번호 - 숫자조합으로 15글자 적용, 예)12545-12345-12365',
  `os_delivery_price` int(10) NOT NULL DEFAULT 0 COMMENT '배송료',
  `os_product_sell_price` int(10) NOT NULL DEFAULT 0 COMMENT '상품판매가격',
  `os_commission` int(10) NOT NULL DEFAULT 0 COMMENT '수수료',
  `os_settle_price` int(10) NOT NULL DEFAULT 0 COMMENT '정산지급액',
  `os_settlestatus` varchar(50) NOT NULL DEFAULT '' COMMENT '주문상품의 정산상태 자격미달,신청가능,신청완료,정산완료',
  `os_settle_app_date` date NOT NULL COMMENT '신청완료일',
  `os_settle_com_date` date NOT NULL COMMENT '정산완료일',
  `os_ommission_type` varchar(50) NOT NULL DEFAULT 'persent' COMMENT '정산유형 (공급가기준, 수수료율기준) - 현재버전에서는 사용되지 않음.',
  PRIMARY KEY (`os_uid`),
  KEY `os_oordernum` (`os_oordernum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상점 정산관련 정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_settle`
--

LOCK TABLES `smart_order_settle` WRITE;
/*!40000 ALTER TABLE `smart_order_settle` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_settle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_settle_complete`
--

DROP TABLE IF EXISTS `smart_order_settle_complete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_settle_complete` (
  `s_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유 id',
  `s_partnerCode` varchar(100) NOT NULL COMMENT '입점업체아이디',
  `s_price` int(11) NOT NULL DEFAULT 0 COMMENT '총 구매 합계 금액',
  `s_delivery_price` int(11) NOT NULL DEFAULT 0 COMMENT '총 배송비',
  `s_com_price` int(11) NOT NULL DEFAULT 0 COMMENT '총 업체 수수료',
  `s_usepoint` int(11) NOT NULL DEFAULT 0 COMMENT '총 할인액',
  `s_discount` int(11) NOT NULL DEFAULT 0 COMMENT '총 수수료',
  `s_count` int(11) NOT NULL DEFAULT 0 COMMENT '총개수',
  `s_tax_mgtnum` varchar(50) NOT NULL COMMENT '세금계산서 연동 - 자체문서관리번호',
  `s_tax_status` int(10) NOT NULL COMMENT '세금계산서 연동 - 상태값 ( /include/addons/barobill/include.var.php 변수 $arr_inner_state_table 참조)',
  `s_price_vat_n` int(11) NOT NULL DEFAULT 0 COMMENT '총 구매 합계 금액',
  `s_delivery_price_vat_n` int(11) NOT NULL DEFAULT 0 COMMENT '총 배송비',
  `s_com_price_vat_n` int(11) NOT NULL DEFAULT 0 COMMENT '총 업체 수수료',
  `s_usepoint_vat_n` int(11) NOT NULL DEFAULT 0 COMMENT '총 할인액',
  `s_discount_vat_n` int(11) NOT NULL DEFAULT 0 COMMENT '총 수수료',
  `s_count_vat_n` int(11) NOT NULL DEFAULT 0 COMMENT '총개수',
  `s_tax_mgtnum_vat_n` varchar(50) NOT NULL COMMENT '세금계산서 연동 - 자체문서관리번호',
  `s_tax_status_vat_n` int(10) NOT NULL COMMENT '계산서 연동 - 상태값 ( /include/addons/barobill/include.var.php 변수 $arr_inner_state_table 참조)',
  `s_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '처리일',
  PRIMARY KEY (`s_uid`),
  KEY `s_partnerCode` (`s_partnerCode`),
  KEY `s_tax_status` (`s_tax_status`),
  KEY `s_tax_status_vat_n` (`s_tax_status_vat_n`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='정산완료테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_settle_complete`
--

LOCK TABLES `smart_order_settle_complete` WRITE;
/*!40000 ALTER TABLE `smart_order_settle_complete` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_settle_complete` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_order_settle_complete_log`
--

DROP TABLE IF EXISTS `smart_order_settle_complete_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_order_settle_complete_log` (
  `sl_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sl_suid` int(10) NOT NULL DEFAULT 0 COMMENT '정산완료 고유번호',
  `sl_mode` varchar(20) NOT NULL DEFAULT 'regist' COMMENT '연동모드 - regist:임시저장, issue:발행, cancel:발행취소, delete:삭제',
  `sl_code` varchar(20) NOT NULL COMMENT '성고 또는 오류코드',
  `sl_remark` varchar(255) DEFAULT NULL COMMENT '비고사항 : 오류코드 - 또는 오류내용',
  `sl_rdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sl_uid`),
  KEY `sl_suid` (`sl_suid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='정산완료 - 바로빌 - 연동로그 ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_order_settle_complete_log`
--

LOCK TABLES `smart_order_settle_complete_log` WRITE;
/*!40000 ALTER TABLE `smart_order_settle_complete_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_order_settle_complete_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_orderbank_log`
--

DROP TABLE IF EXISTS `smart_orderbank_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_orderbank_log` (
  `ob_uid` int(11) NOT NULL AUTO_INCREMENT,
  `ob_ordernum` varchar(20) NOT NULL COMMENT '주문번호',
  `ob_tid` varchar(100) NOT NULL,
  `ob_content` text NOT NULL,
  `ob_status` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Y : 입금확인, N : 입금미확인',
  `ob_status_type` enum('order','adminO','adminC','ready') NOT NULL DEFAULT 'ready' COMMENT '처리상태 order: 정상주문연동, adminO: 관리자주문연동, adminC: 관리자확인, ready: 처리대기',
  `ob_paydate` datetime NOT NULL COMMENT '실제 입금이 확인된 날짜',
  `ob_date` datetime NOT NULL,
  `ob_ordername` varchar(50) NOT NULL COMMENT '로그 저장 이름',
  `ob_orderprice` int(11) NOT NULL DEFAULT 0 COMMENT '로그 주문금액',
  `ob_account` varchar(50) NOT NULL COMMENT '입금계좌번호',
  `ob_memo` text NOT NULL COMMENT '관리자메모사항',
  `ob_deleted` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '삭제여부',
  PRIMARY KEY (`ob_uid`),
  KEY `ob_ordernum` (`ob_ordernum`),
  KEY `ob_deleted` (`ob_deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='자동입금로그관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_orderbank_log`
--

LOCK TABLES `smart_orderbank_log` WRITE;
/*!40000 ALTER TABLE `smart_orderbank_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_orderbank_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_payco_log`
--

DROP TABLE IF EXISTS `smart_payco_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_payco_log` (
  `pl_uid` int(11) NOT NULL AUTO_INCREMENT,
  `pl_code` varchar(50) NOT NULL COMMENT '결과코드',
  `pl_ono` varchar(255) NOT NULL COMMENT '페이코에서 결제최종 승인 후 발급한 페이코 주문번호(취소시 필요)',
  `pl_certifykey` varchar(255) NOT NULL COMMENT '주문인증키(취소시 필요)',
  `pl_oordernum` varchar(20) NOT NULL COMMENT '가맹점 주문번호',
  `pl_rordernum` varchar(255) NOT NULL COMMENT '페이코 주문번호',
  `pl_type` enum('request','auth','nonbank') NOT NULL COMMENT '요청타입(request:결제인증,auth:결제승인,nonbank:무통장입금통보)',
  `pl_serialize` text NOT NULL COMMENT 'serialize 데이터',
  `pl_rdate` datetime NOT NULL COMMENT '기록일',
  PRIMARY KEY (`pl_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='페이코로그테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_payco_log`
--

LOCK TABLES `smart_payco_log` WRITE;
/*!40000 ALTER TABLE `smart_payco_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_payco_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_point_log`
--

DROP TABLE IF EXISTS `smart_point_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_point_log` (
  `pl_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pl_inid` varchar(50) NOT NULL COMMENT '제공받은 개인회원',
  `pl_title` varchar(100) NOT NULL COMMENT '제공받은 타이틀',
  `pl_point` int(10) DEFAULT 0 COMMENT '제공 포인트량',
  `pl_point_before` int(10) NOT NULL DEFAULT 0 COMMENT '포인트 처리 전 회원포인트',
  `pl_point_apply` int(10) NOT NULL DEFAULT 0 COMMENT '실제 적용 포인트',
  `pl_point_after` int(10) NOT NULL DEFAULT 0 COMMENT '포인트 처리 후 회원포인트',
  `pl_status` enum('Y','N','C') DEFAULT 'N' COMMENT '포인트 지급 여부(Y:지급완료, N:지급대기, C:지급취소)',
  `pl_appdate` date DEFAULT NULL COMMENT '포인트 지급예정일',
  `pl_rdate` datetime DEFAULT NULL COMMENT '등록일',
  `pl_adate` datetime NOT NULL COMMENT '실제 적립금 지급 일시',
  `pl_delete` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '삭제여부',
  PRIMARY KEY (`pl_uid`),
  KEY `pl_inid` (`pl_inid`),
  KEY `pl_status` (`pl_status`),
  KEY `pl_delete` (`pl_delete`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='포인트기록관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_point_log`
--

LOCK TABLES `smart_point_log` WRITE;
/*!40000 ALTER TABLE `smart_point_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_point_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_policy`
--

DROP TABLE IF EXISTS `smart_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_policy` (
  `po_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `po_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '정책사용여부',
  `po_name` varchar(50) NOT NULL COMMENT '칼럼명',
  `po_title` varchar(255) NOT NULL COMMENT '항목타이틀',
  `po_content` text NOT NULL COMMENT '세부내용',
  PRIMARY KEY (`po_uid`),
  KEY `po_name` (`po_name`),
  KEY `po_use` (`po_use`)
) ENGINE=MyISAM AUTO_INCREMENT=411 DEFAULT CHARSET=utf8 COMMENT='사이트 이용약관 등 정책관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_policy`
--

LOCK TABLES `smart_policy` WRITE;
/*!40000 ALTER TABLE `smart_policy` DISABLE KEYS */;
INSERT INTO `smart_policy` VALUES (19,'Y','guest_order','[필수] 개인정보수집 및 이용 동의(비회원 주문)','[쇼핑몰 기본정보::회사명](이하 \\\'회사\\\')은 고객님의 개인정보를 중요시하며, \\\"정보통신망 이용촉진 및 정보보호\\\"에 관한 법률을 준수하고 있습니다.\r\n \r\n〈개인정보의 수집 및 이용목적〉\r\n\r\n 회사는 수집한 개인정보를 다음의 목적을 위해 활용합니다.\r\n - 서비스 제공에 관한 계약 이행 및 서비스 제공에 따른 요금정산, 콘텐츠 제공, 구매 및 요금 결제 \r\n 물품배송 또는 청구지 등 발송, 금융거래 본인 인증 및 금융 서비스\r\n - 회원 관리\r\n 회원제 서비스 이용에 따른 본인확인, 개인 식별, 불량회원의 부정 이용 방지와 비인가 사용 방지, 가입 의사 확인, 연령확인 \r\n 불만처리 등 민원처리, 고지사항 전달\r\n - 마케팅 및 광고에 활용\r\n 이벤트 등 광고성 정보 전달 , 접속 빈도 파악 또는 회원의 서비스 이용에 대한 통계\r\n \r\n〈개인정보의 보유 및 이용기간〉\r\n\r\n 원칙적으로, 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체 없이 파기합니다.\r\n 단, 관계법령의 규정에 의하여 보존할 필요가 있는 경우 회사는 아래와 같이 관계법령에서 정한 일정한 기간 동안 회원정보를 보관합니다.\r\n 보존 항목 : 이름, 로그인ID, 비밀번호, 자택 전화번호, 자택 주소, 휴대전화번호, 이메일, 결제기록\r\n 보존 근거 : 전자상거래등에서의 소비자보호에 관한 법률\r\n 보존 기간 : 5년\r\n 표시/광고에 관한 기록 : 6개월 (전자상거래등에서의 소비자보호에 관한 법률)\r\n 계약 또는 청약철회 등에 관한 기록 : 5년 (전자상거래등에서의 소비자보호에 관한 법률)\r\n 대금결제 및 재화 등의 공급에 관한 기록 : 5년 (전자상거래등에서의 소비자보호에 관한 법률)\r\n 소비자의 불만 또는 분쟁처리에 관한 기록 : 3년 (전자상거래등에서의 소비자보호에 관한 법률)\r\n 신용정보의 수집/처리 및 이용 등에 관한 기록 : 3년 (신용정보의 이용 및 보호에 관한 법률)\r\n \r\n〈개인정보의 파기절차 및 방법〉\r\n\r\n 회사는 원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체없이 파기합니다.\r\n 파기절차 및 방법은 다음과 같습니다.\r\n - 파기절차\r\n 회원님이 회원가입 등을 위해 입력하신 정보는 목적이 달성된 후 별도의 DB로 옮겨져(종이의 경우 별도의 서류함)\r\n 내부 방침 및 기타 관련 법령에 의한 정보보호 사유에 따라(보유 및 이용기간 참조) 일정 기간 저장된 후 파기되어집니다.\r\n 별도 DB로 옮겨진 개인정보는 법률에 의한 경우가 아니고서는 보유되어지는 이외의 다른 목적으로 이용되지 않습니다.\r\n - 파기방법\r\n 전자적 파일형태로 저장된 개인정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제합니다.		\r\n\\\"몰\\\"과 이용자간에 제기된 전자거래 소송에는 한국법을 적용합니다.'),(20,'Y','guest_board','[필수] 개인정보수집 및 이용 동의(비회원 글쓰기)','개인정보 수집 및 이용에 관한 안내\r\n\r\n1. 수집하는 개인정보의 항목\r\n이름, 휴대폰, 이메일\r\n\r\n2. 개인정보의 수집 및 이용 목적\r\n\\\'회사\\\'가 사용자들의 개인정보를 수집하는 목적은 이용자들의 신분을 확인하고 \\\'서비스\\\'를 이용하고자 하는 의사확인을 위한 것이며, 이용자들에게 최적의 서비스를 제공하고자 하는 것입니다. \\\'회사\\\'는 아래와 같이 수집목적에 따라 개인정보를 이용하고 있습니다. 다만, 이용목적은 수집목적을 벗어나지 않는 범위에서 추가 서비스제공을 위해 확장될 수 있으며, 이 경우에는 미리 공지하여 그 사실을 알려 반드시 고객님께 사전 동의를 구할 것입니다.\r\n\r\n3. 개인정보의 보유 및 이용기간\r\n개인정보는 수집 및 이용목적 외의 용도로 이용되지 않으며, 관련 담당자를 제외하고는 함부로 열람할 수 없습니다.\r\n개인정보는 동의 철회 시점까지 보관 됩니다.'),(21,'Y','partner_agree','[필수] 개인정보수집 및 이용 동의(광고/제휴문의)','개인정보 수집 및 이용에 관한 안내\r\n\r\n1. 수집하는 개인정보의 항목\r\n이름/상호명, 연락처, 이메일 주소\r\n\r\n2. 개인정보의 수집 및 이용 목적\r\n\\\'회사\\\'가 사용자들의 개인정보를 수집하는 목적은 이용자들의 신분을 확인하고 \\\'서비스\\\'를 이용하고자 하는 의사확인을 위한 것이며, 이용자들에게 최적의 서비스를 제공하고자 하는 것입니다. \\\'회사\\\'는 아래와 같이 수집목적에 따라 개인정보를 이용하고 있습니다. 다만, 이용목적은 수집목적을 벗어나지 않는 범위에서 추가 서비스제공을 위해 확장될 수 있으며, 이 경우에는 미리 공지하여 그 사실을 알려 반드시 고객님께 사전 동의를 구할 것입니다.\r\n\r\n3. 개인정보의 보유 및 이용기간\r\n개인정보는 수집 및 이용목적 외의 용도로 이용되지 않으며, 관련 담당자를 제외하고는 함부로 열람할 수 없습니다.\r\n개인정보는 동의 철회 시점까지 보관 됩니다.'),(9,'Y','agree','이용약관(텍스트)','제1조(목적)\r\n\r\n이 약관은 [쇼핑몰 기본정보::회사명](전자거래 사업자)에서 운영하는 쇼핑몰(이하 \\\"몰\\\"이라 한다)에서 제공하는 인터넷 관련 서비스(이하 \\\"서비스\\\"라 한다)를 이용함에 있어 사이버몰과 이용자의 권리·의무 및 책임사항을 규정함을 목적으로 합니다.\r\n※ 「PC통신등을 이용하는 전자거래에 대해서도 그 성질에 반하지 않는한 이 약관을 준용합니다」\r\n \r\n제2조(정의)\r\n \r\n① \\\"몰\\\" 이란 회사가 재화 또는 용역을 이용자에게 제공하기 위하여 컴퓨터등 정보통신설비를 이용하여 재화 또는 용역을 거래할 수 있도록 설정한 가상의 영업장을 말하며, 아울러 사이버몰을 운영하는 사업자의 의미로도 사용합니다.\r\n② \\\"이용자\\\"란 \\\"몰\\\"에 접속하여 이 약관에 따라 \\\"몰\\\"이 제공하는 서비스를 받는 회원 및 비회원을 말합니다.\r\n③ 회원이라 함은 \\\"몰\\\"에 개인정보를 제공하여 회원등록을 한 자로서, \\\"몰\\\"의 정보를 지속적으로 제공받으며, \\\"몰\\\"이 제공하는 서비스를 계속적으로 이용할 수 있는 자를 말합니다.\r\n④ 비회원이라 함은 회원에 가입하지 않고 \\\"몰\\\"이 제공하는 서비스를 이용하는 자를 말합니다.\r\n \r\n제3조 (약관의 명시와 개정)\r\n \r\n① \\\"몰\\\"은 이 약관의 내용과 상호, 영업소 소재지, 대표자의 성명, 사업자등록번호, 연락처(전화, 팩스, 전자우편 주소 등) 등을 이용자가 알 수 있도록 사이버몰의 초기 서비스화면(전면)에 게시합니다.\r\n② \\\"몰\\\"은 약관의규제에관한법률, 전자거래기본법, 전자서명법, 정보통신망이용촉진등에관한법률, 방문판매등에관한법률, 소비자보호법 등 관련법을 위배하지 않는 범위에서 이 약관을 개정할 수 있습니다.\r\n③ \\\"몰\\\"이 약관을 개정할 경우에는 적용일자 및 개정사유를 명시하여 현행약관과 함께 몰의 초기화면에 그 적용일자 7일이전부터 적용일자 전일까지 공지합니다.\r\n④ \\\"몰\\\"이 약관을 개정할 경우에는 그 개정약관은 그 적용일자 이후에 체결되는 계약에만 적용되고 그 이전에 이미 체결된 계약에 대해서는 개정전의 약관조항이 그대로 적용됩니다. 다만 이미 계약을 체결한 이용자가 개정약관 조항의 적용을 받기를 원하는 뜻을 제3항에 의한 개정약관의 공지기간내에 \\\'몰\\\"에 송신하여 \\\"몰\\\"의 동의를 받은 경우에는 개정약관 조항이 적용됩니다.\r\n⑤ 이 약관에서 정하지 아니한 사항과 이 약관의 해석에 관하여는 정부가 제정한 전자거래소비자보호지침 및 관계법령 또는 상관례에 따릅니다.\r\n \r\n제4조(서비스의 제공 및 변경)\r\n \r\n① \\\"몰\\\"은 다음과 같은 업무를 수행합니다. - 재화 또는 용역에 대한 정보 제공 및 구매계약의 체결 - 구매계약이 체결된 재화 또는 용역의 배송 - 기타 \\\"몰\\\"이 정하는 업무 \r\n② \\\"몰\\\"은 재화의 품절 또는 기술적 사양의 변경 등의 경우에는 장차 체결되는 계약에 의해 제공할 재화·용역의 내용을 변경할 수 있습니다. 이 경우에는 변경된 재화·용역의 내용 및 제공일자를 명시하여 현재의 재화·용역의 내용을 게시한 곳에 그 제공일자 이전 7일부터 공지합니다.\r\n \r\n제5조(서비스의 중단)\r\n \r\n① \\\"몰\\\"은 컴퓨터 등 정보통신설비의 보수점검·교체 및 고장, 통신의 두절 등의 사유가 발생한 경우에는 서비스의 제공을 일시적으로 중단할 수 있습니다.\r\n② 제1항에 의한 서비스 중단의 경우에는 \\\"몰\\\"은 제8조에 정한 방법으로 이용자에게 통지합니다.\r\n \r\n제6조(회원가입)\r\n \r\n① 이용자는 \\\"몰\\\"이 정한 가입 양식에 따라 회원정보를 기입한 후 이 약관에 동의한다는 의사표시를 함으로서 회원가입을 신청합니다.\r\n② \\\"몰\\\"은 제1항과 같이 회원으로 가입할 것을 신청한 이용자 중 다음 각호에 해당하지 않는 한 회원으로 등록합니다. - 가입신청자가 이 약관 제7조 제3항에 의하여 이전에 회원자격을 상실한 적이 있는 경우, 다만 제7조 제3항에 의한 회원자격 상실후 3년이 경과한 자로서 \\\"몰\\\"의 회원재가입 승낙을 얻은 경우에는 예외로 한다. - 등록 내용에 허위, 기재누락, 오기가 있는 경우 - 기타 회원으로 등록하는 것이 \\\"몰\\\"의 기술상 현저히 지장이 있다고 판단되는 경우\r\n③ 회원가입계약의 성립시기는 \\\"몰\\\"의 승낙이 회원에게 도달한 시점으로 합니다.\r\n④ 회원은 제15조제1항에 의한 등록사항에 변경이 있는 경우, 즉시 전자우편이나 기타 방법으로 \\\"몰\\\"에 대하여 그 변경사항을 알려야 합니다.\r\n \r\n제7조(회원 탈퇴 및 자격 상실 등)\r\n \r\n① 회원은 \\\"몰\\\"에 언제든지 탈퇴를 요청할 수 있으며 \\\"몰\\\"은 즉시 회원탈퇴를 처리합니다.\r\n② 회원이 다음 각호의 사유에 해당하는 경우, \\\"몰\\\"은 회원자격을 제한 및 정지시킬 수 있습니다. - 가입 신청시에 허위 내용을 등록한 경우 - \\\"몰\\\"을 이용하여 구입한 재화·용역 등의 대금, 기타 \\\"몰\\\"이용에 관련하여 회원이 부담하는 채무를 기일에 지급하지 않는 경우 - 다른 사람의 \\\"몰\\\" 이용을 방해하거나 그 정보를 도용하는 등 전자거래질서를 위협하는 경우 - \\\"몰\\\"을 이용하여 법령과 이 약관이 금지하거나 공서양속에 반하는 행위를 하는 경우\r\n③ \\\"몰\\\"이 회원 자격을 제한·정지 시킨후, 동일한 행위가 2회이상 반복되거나 30일이내에 그 사유가 시정되지 아니하는 경우 \\\"몰\\\"은 회원자격을 상실시킬 수 있습니다.\r\n④ \\\"몰\\\"이 회원자격을 상실시키는 경우에는 회원등록을 말소합니다. 이 경우 회원에게 이를 통지하고, 회원등록 말소전에 소명할 기회를 부여합니다.\r\n \r\n제8조(회원에 대한 통지)\r\n \r\n① \\\"몰\\\"이 회원에 대한 통지를 하는 경우, 회원이 \\\"몰\\\"에 제출한 전자우편 주소 또는 기타 방법으로 할 수 있습니다.\r\n② \\\"몰\\\"은 불특정다수 회원에 대한 통지의 경우 1주일이상 \\\"몰\\\" 게시판에 게시함으로서 개별 통지에 갈음할 수 있습니다.\r\n \r\n제9조(구매신청)\r\n \r\n\\\"몰\\\"이용자는 \\\"몰\\\"상에서 이하의 방법에 의하여 구매를 신청합니다.\r\n- 구매를 위해 필요한 최소한의 정보 (성명, 주소, 전화번호 입력등)\r\n- 재화 또는 용역의 선택 - 결제방법의 선택\r\n- 이 약관에 동의한다는 표시(예, 마우스 클릭)\r\n \r\n제10조 (계약의 성립)\r\n \r\n① \\\"몰\\\"은 제9조와 같은 구매신청에 대하여 다음 각호에 해당하지 않는 한 승낙합니다. - 신청 내용에 허위, 기재누락, 오기가 있는 경우 - 미성년자가 담배, 주류등 청소년보호법에서 금지하는 재화 및 용역을 구매하는 경우 - 기타 구매신청에 승낙하는 것이 \\\"몰\\\" 기술상 현저히 지장이 있다고 판단하는 경우 - \\\"몰\\\"이 판단하여 판매가 부적합 하다고 판단되는 구매자의 경우 - \\\"몰\\\"이 준비한 수량이상으로 구매하여 재화 또는 용역을 제공하지 못한다고 판단하는 경우\r\n② \\\"몰\\\"의 승낙이 제12조제1항의 수신확인통지형태로 이용자에게 도달한 시점에 계약이 성립한 것으로 봅니다.\r\n \r\n제11조(지급방법)\r\n \r\n몰\\\"에서 구매한 재화 또는 용역에 대한 대금지급방법은 다음 각호의 하나로 할 수 있습니다.\r\n- 신용카드결제\r\n- 계좌이체\r\n- 무통장입금\r\n \r\n제12조(수신확인통지·구매신청 변경 및 취소)\r\n \r\n① \\\"몰\\\"은 이용자의 구매신청이 있는 경우 이용자에게 수신확인통지를 합니다.\r\n② 수신확인통지를 받은 이용자는 의사표시의 불일치등이 있는 경우에는 수신확인통지를 받은 후 즉시 구매신청 변경 및 취소를 요청할 수 있습니다.\r\n③ \\\"몰\\\"은 배송전 이용자의 구매신청 변경 및 취소 요청이 있는 때에는 지체없이 그 요청에 따라 처리합니다.\r\n \r\n제13조(배송)\r\n \r\n① \\\"몰\\\"은 이용자와 재화등의 공급시기에 관하여 별도의 약정이 없는 이상, 이용자가 청약을 한 날부터 7일 이내에 재화 등을 배송할 수 있도록 주문제작, 포장 등 기타의 필요한 조치를 취합니다.  다만, \\\"몰\\\"이 이미 재화 등의 대금의 전부 또는 일부를 받은 경우에는 대금의 전부 또는 일부를 받은 날부터 3영업일 이내에 조치를 취합니다.  이때 \\\"몰\\\"은 이용자가 재화등의 공급 절차 및 진행 사항을 확인할 수 있도록 적절한 조치를 합니다.\r\n② \\\"몰\\\"은 이용자가 구매한 재화에 대해 배송수단, 수단별 배송비용 부담자, 수단별 배송기간 등을 명시합니다. 만약 \\\"몰\\\"이 약정 배송기간을 초과한 경우에는 그로 인한 이용자의 손해를 배상 하여야 합니다. 다만 \\\"몰\\\"이 고의·과실이 없음을 입증한 경우에는 그러하지 아니합니다.\r\n \r\n제14조(환급, 반품 및 교환)\r\n \r\n① \\\"몰\\\"은 이용자가 구매신청한 재화 또는 용역이 품절등의 사유로 재화의 인도 또는 용역의 제공을 할 수 없을 때에는 지체없이 그 사유를 이용자에게 통지하고, 사전에 재화 또는 용역의 대금을 받은 경우에는 대금을 받은 날부터 영업3일이내에, 그렇지 않은 경우에는 그 사유발생일로부터 영업3일이내에 계약해제 및 환급절차를 취합니다.\r\n② 다음 각호의 경우에는 \\\"몰\\\"은 배송된 재화일지라도 재화를 반품받은 다음 영업3일 이내에 이용자의 요구에 따라 즉시 환급, 반품 및 교환 조치를 합니다. 다만 그 요구기한은 배송된 날로부터 20일 이내로 합니다.\r\n- 배송된 재화가 주문내용과 상이하거나 \\\"몰\\\"이 제공한 정보와 상이할 경우\r\n- 배송된 재화가 파손, 손상되었거나 오염되었을 경우\r\n- 재화가 광고에 표시된 배송기간보다 늦게 배송된 경우\r\n- 방문판매등에관한법률 제18조에 의하여 광고에 표시하여야 할 사항을 표시하지 아니한 상태에서 이용자의 청약이 이루어진 경우\r\n \r\n제15조(개인정보보호)\r\n \r\n① \\\"몰\\\"은 이용자의 정보수집시 구매계약 이행에 필요한 최소한의 정보를 수집합니다. 회원가입시 필수항목으로 지정된 정보 이외의 사항은 선택사항으로 합니다.\r\n② \\\"몰\\\"이 이용자의 개인식별이 가능한 개인정보를 수집하는 때에는 반드시 당해 이용자의 동의를 받습니다.\r\n③ 제공된 개인정보는 당해 이용자의 동의없이 목적외의 이용이나 제3자에게 제공할 수 없으며, 이에 대한 모든 책임은 \\\"몰\\\"이 집니다. 다만, 다음의 경우에는 예외로 합니다. - 배송업무상 배송업체에게 배송에 필요한 최소한의 이용자의 정보(성명, 주소, 전화번호)를 알려주는 경우 - 통계작성, 학술연구 또는 시장조사를 위하여 필요한 경우로서 특정 개인을 식별할 수 없는 형태로 제공하는 경우\r\n④ \\\"몰\\\"이 제2항과 제3항에 의해 이용자의 동의를 받아야 하는 경우에는 개인정보관리 책임자의 신원(소속, 성명 및 전화번호 기타 연락처), 정보의 수집목적 및 이용목적, 제3자에 대한 정보제공 관련사항(제공받는자, 제공목적 및 제공할 정보의 내용)등 정보통신망이용촉진등에관한법률 제16조제3항이 규정한 사항을 미리 명시하거나 고지해야 하며 이용자는 언제든지 이 동의를 철회할 수 있습니다.\r\n⑤ 이용자는 언제든지 \\\"몰\\\"이 가지고 있는 자신의 개인정보에 대해 열람 및 오류정정을 요구할 수 있으며 \\\"몰\\\"은 이에 대해 지체없이 필요한 조치를 취할 의무를 집니다. 이용자가 오류의 정정을 요구한 경우에는 \\\"몰\\\"은 그 오류를 정정할 때까지 당해 개인정보를 이용하지 않습니다.\r\n⑥ \\\"몰\\\"은 개인정보 보호를 위하여 관리자를 한정하여 그 수를 최소화하며 신용카드, 은행계좌 등을 포함한 이용자의 개인정보의 분실, 도난, 유출, 변조 등으로 인한 이용자의 손해에 대하여 모든 책임을 집니다.\r\n⑦ \\\"몰\\\" 또는 그로부터 개인정보를 제공받은 제3자는 개인정보의 수집목적 또는 제공받은 목적을 달성한 때에는 당해 개인정보를 지체없이 파기합니다.\r\n \r\n제16조(\\\"몰\\\"의 의무)\r\n \r\n① \\\"몰은 법령과 이 약관이 금지하거나 공서양속에 반하는 행위를 하지 않으며 이 약관이 정하는 바에 따라 지속적이고, 안정적으로 재화·용역을 제공하는 데 최선을 다하여야 합니다.\r\n② \\\"몰\\\"은 이용자가 안전하게 인터넷 서비스를 이용할 수 있도록 이용자의 개인정보(신용정보 포함)보호를 위한 보안 시스템을 갖추어야 합니다.\r\n③ \\\"몰\\\"이 상품이나 용역에 대하여 「표시·광고의공정화에관한법률」 제3조 소정의 부당한 표시·광고행위를 함으로써 이용자가 손해를 입은 때에는 이를 배상할 책임을 집니다.\r\n④ \\\"몰\\\"은 이용자가 원하지 않는 영리목적의 광고성 전자우편을 발송하지 않습니다.\r\n \r\n제17조( 회원의 ID 및 비밀번호에 대한 의무)\r\n \r\n① 제15조의 경우를 제외한 ID와 비밀번호에 관한 관리책임은 회원에게 있습니다.\r\n② 회원은 자신의 ID 및 비밀번호를 제3자에게 이용하게 해서는 안됩니다.\r\n③ 회원이 자신의 ID 및 비밀번호를 도난당하거나 제3자가 사용하고 있음을 인지한 경우에는 바로 \\\"몰\\\"에 통보하고 \\\"몰\\\"의 안내가 있는 경우에는 그에 따라야 합니다.\r\n \r\n제18조(이용자의 의무)\r\n \r\n이용자는 다음 행위를 하여서는 안됩니다.\r\n- 신청 또는 변경시 허위내용의 등록 - \\\"몰\\\"에 게시된 정보의 변경\r\n- \\\"몰\\\"이 정한 정보 이외의 정보(컴퓨터 프로그램 등)의 송신 또는 게시\r\n- \\\"몰\\\" 기타 제3자의 저작권 등 지적재산권에 대한 침해\r\n- \\\"몰\\\" 기타 제3자의 명예를 손상시키거나 업무를 방해하는 행위\r\n- 외설 또는 폭력적인 메시지·화상·음성 기타 공서양속에 반하는 정보를 몰에 공개 또는 게시하는 행위\r\n \r\n제19조(연결\\\"몰\\\"과 피연결\\\"몰\\\" 간의 관계)\r\n \r\n① 상위 \\\"몰\\\"과 하위 \\\"몰\\\"이 하이퍼 링크(예: 하이퍼 링크의 대상에는 문자, 그림 및 동화상 등이 포함됨)방식 등으로 연결된 경우, 전자를 연결 \\\"몰\\\"(웹 사이트)이라고 하고 후자를 피연결 \\\"몰\\\"(웹사이트)이라고 합니다.\r\n② 연결 \\\"몰\\\"은 피연결 \\\"몰\\\"이 독자적으로 제공하는 재화·용역에 의하여 이용자와 행하는 거래에 대해서 보증책임을지지 않는다는 뜻을 연결 \\\"몰\\\"의 사이트에서 명시한 경우에는 그 거래에 대한 보증책임을지지 않습니다.\r\n \r\n제20조(저작권의 귀속 및 이용제한)\r\n \r\n① \\\"몰\\\"이 작성한 저작물에 대한 저작권 기타 지적재산권은 \\\"몰\\\"에 귀속합니다.\r\n② 이용자는 \\\"몰\\\"을 이용함으로써 얻은 정보를 \\\"몰\\\"의 사전 승낙없이 복제, 송신, 출판, 배포, 방송 기타 방법에 의하여 영리목적으로 이용하거나 제3자에게 이용하게 하여서는 안됩니다.\r\n \r\n제21조(분쟁해결)\r\n \r\n① \\\"몰\\\"은 이용자가 제기하는 정당한 의견이나 불만을 반영하고 그 피해를 보상처리하기 위하여 피해보상처리기구를 설치·운영합니다.\r\n② \\\"몰\\\"은 이용자로부터 제출되는 불만사항 및 의견은 우선적으로 그 사항을 처리합니다. 다만, 신속한 처리가 곤란한 경우에는 이용자에게 그 사유와 처리일정을 즉시 통보해 드립니다.\r\n③ \\\"몰\\\"과 이용자간에 발생한 분쟁은 전자거래기본법 제28조 및 동 시행령 제15조에 의하여 설치된 전자거래분쟁조정위원회의 조정에 따를 수 있습니다.\r\n \r\n제22조(재판권 및 준거법)\r\n \r\n① \\\"몰\\\"과 이용자간에 발생한 전자거래 분쟁에 관한 소송은 민사소송법상의 관할법원에 제기합니다.\r\n② \\\"몰\\\"과 이용자간에 제기된 전자거래 소송에는 한국법을 적용합니다.'),(11,'Y','agree_html_m','이용약관(MOBILE)','<p><span style=\\\"font-size: 15px; font-weight: 600; letter-spacing: -0.5px; color: rgb(0, 0, 0);\\\">제1조(목적)</span>&nbsp;</p><br><p>이 약관은 [쇼핑몰 기본정보::회사명](전자거래 사업자)에서 운영하는 쇼핑몰(이하 \\\"몰\\\"이라 한다)에서 제공하는 인터넷 관련 서비스(이하 \\\"서비스\\\"라 한다)를 이용함에 있어 사이버몰과 이용자의 권리·의무 및 책임사항을 규정함을 목적으로 합니다.<br>※ 「PC통신등을 이용하는 전자거래에 대해서도 그 성질에 반하지 않는한 이 약관을 준용합니다」<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제2조(정의)</span></p><br><p>① \\\"몰\\\" 이란 회사가 재화 또는 용역을 이용자에게 제공하기 위하여 컴퓨터등 정보통신설비를 이용하여 재화 또는 용역을 거래할 수 있도록 설정한 가상의 영업장을 말하며, 아울러 사이버몰을 운영하는 사업자의 의미로도 사용합니다.<br>② \\\"이용자\\\"란 \\\"몰\\\"에 접속하여 이 약관에 따라 \\\"몰\\\"이 제공하는 서비스를 받는 회원 및 비회원을 말합니다.<br>③ 회원\\\'이라 함은 \\\"몰\\\"에 개인정보를 제공하여 회원등록을 한 자로서, \\\"몰\\\"의 정보를 지속적으로 제공받으며, \\\"몰\\\"이 제공하는 서비스를 계속적으로 이용할 수 있는 자를 말합니다.<br>④ 비회원\\\'이라 함은 회원에 가입하지 않고 \\\"몰\\\"이 제공하는 서비스를 이용하는 자를 말합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제3조 (약관의 명시와 개정)</span></p><br><p>① \\\"몰\\\"은 이 약관의 내용과 상호, 영업소 소재지, 대표자의 성명, 사업자등록번호, 연락처(전화, 팩스, 전자우편 주소 등) 등을 이용자가 알 수 있도록 사이버몰의 초기 서비스화면(전면)에 게시합니다.<br>② \\\"몰\\\"은 약관의규제에관한법률, 전자거래기본법, 전자서명법, 정보통신망이용촉진등에관한법률, 방문판매등에관한법률, 소비자보호법 등 관련법을 위배하지 않는 범위에서 이 약관을 개정할 수 있습니다.<br>③ \\\"몰\\\"이 약관을 개정할 경우에는 적용일자 및 개정사유를 명시하여 현행약관과 함께 몰의 초기화면에 그 적용일자 7일이전부터 적용일자 전일까지 공지합니다.<br>④ \\\"몰\\\"이 약관을 개정할 경우에는 그 개정약관은 그 적용일자 이후에 체결되는 계약에만 적용되고 그 이전에 이미 체결된 계약에 대해서는 개정전의 약관조항이 그대로 적용됩니다. 다만 이미 계약을 체결한 이용자가 개정약관 조항의 적용을 받기를 원하는 뜻을 제3항에 의한 개정약관의 공지기간내에 \\\'몰\\\"에 송신하여 \\\"몰\\\"의 동의를 받은 경우에는 개정약관 조항이 적용됩니다.<br>⑤ 이 약관에서 정하지 아니한 사항과 이 약관의 해석에 관하여는 정부가 제정한 전자거래소비자보호지침 및 관계법령 또는 상관례에 따릅니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제4조(서비스의 제공 및 변경)</span></p><br><p>① \\\"몰\\\"은 다음과 같은 업무를 수행합니다.<br>- 재화 또는 용역에 대한 정보 제공 및 구매계약의 체결<br>- 구매계약이 체결된 재화 또는 용역의 배송<br>- 기타 \\\"몰\\\"이 정하는 업무<br>② \\\"몰\\\"은 재화의 품절 또는 기술적 사양의 변경 등의 경우에는 장차 체결되는 계약에 의해 제공할 재화·용역의 내용을 변경할 수 있습니다. 이 경우에는 변경된 재화·용역의 내용 및 제공일자를 명시하여 현재의 재화·용역의 내용을 게시한 곳에 그 제공일자 이전 7일부터 공지합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제5조(서비스의 중단)</span></p><br><p>① \\\"몰\\\"은 컴퓨터 등 정보통신설비의 보수점검·교체 및 고장, 통신의 두절 등의 사유가 발생한 경우에는 서비스의 제공을 일시적으로 중단할 수 있습니다.<br>② 제1항에 의한 서비스 중단의 경우에는 \\\"몰\\\"은 제8조에 정한 방법으로 이용자에게 통지합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제6조(회원가입)</span></p><br><p>① 이용자는 \\\"몰\\\"이 정한 가입 양식에 따라 회원정보를 기입한 후 이 약관에 동의한다는 의사표시를 함으로서 회원가입을 신청합니다.<br>② \\\"몰\\\"은 제1항과 같이 회원으로 가입할 것을 신청한 이용자 중 다음 각호에 해당하지 않는 한 회원으로 등록합니다.<br>- 가입신청자가 이 약관 제7조 제3항에 의하여 이전에 회원자격을 상실한 적이 있는 경우, 다만 제7조 제3항에 의한 회원자격 상실후 3년이 경과한 자로서 \\\"몰\\\"의 회원재가입 승낙을 얻은 경우에는 예외로 한다.<br>- 등록 내용에 허위, 기재누락, 오기가 있는 경우<br>- 기타 회원으로 등록하는 것이 \\\"몰\\\"의 기술상 현저히 지장이 있다고 판단되는 경우<br>③ 회원가입계약의 성립시기는 \\\"몰\\\"의 승낙이 회원에게 도달한 시점으로 합니다.<br>④ 회원은 제15조제1항에 의한 등록사항에 변경이 있는 경우, 즉시 전자우편이나 기타 방법으로 \\\"몰\\\"에 대하여 그 변경사항을 알려야 합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제7조(회원 탈퇴 및 자격 상실 등)</span></p><br><p>① 회원은 \\\"몰\\\"에 언제든지 탈퇴를 요청할 수 있으며 \\\"몰\\\"은 즉시 회원탈퇴를 처리합니다.<br>② 회원이 다음 각호의 사유에 해당하는 경우, \\\"몰\\\"은 회원자격을 제한 및 정지시킬 수 있습니다.<br>- 가입 신청시에 허위 내용을 등록한 경우<br>- \\\"몰\\\"을 이용하여 구입한 재화·용역 등의 대금, 기타 \\\"몰\\\"이용에 관련하여 회원이 부담하는 채무를 기일에 지급하지 않는 경우<br>- 다른 사람의 \\\"몰\\\" 이용을 방해하거나 그 정보를 도용하는 등 전자거래질서를 위협하는 경우<br>- \\\"몰\\\"을 이용하여 법령과 이 약관이 금지하거나 공서양속에 반하는 행위를 하는 경우<br>③ \\\"몰\\\"이 회원 자격을 제한·정지 시킨후, 동일한 행위가 2회이상 반복되거나 30일이내에 그 사유가 시정되지 아니하는 경우 \\\"몰\\\"은 회원자격을 상실시킬 수 있습니다.<br>④ \\\"몰\\\"이 회원자격을 상실시키는 경우에는 회원등록을 말소합니다. 이 경우 회원에게 이를 통지하고, 회원등록 말소전에 소명할 기회를 부여합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제8조(회원에 대한 통지)</span></p><br><p>① \\\"몰\\\"이 회원에 대한 통지를 하는 경우, 회원이 \\\"몰\\\"에 제출한 전자우편 주소 또는 기타 방법으로 할 수 있습니다.<br>② \\\"몰\\\"은 불특정다수 회원에 대한 통지의 경우 1주일이상 \\\"몰\\\" 게시판에 게시함으로서 개별 통지에 갈음할 수 있습니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제9조(구매신청)</span></p><br><p>\\\"몰\\\"이용자는 \\\"몰\\\"상에서 이하의 방법에 의하여 구매를 신청합니다.<br>- 구매를 위해 필요한 최소한의 정보 (성명, 주소, 전화번호 입력등)<br>- 재화 또는 용역의 선택<br>- 결제방법의 선택<br>- 이 약관에 동의한다는 표시(예, 마우스 클릭)<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제10조 (계약의 성립)</span></p><br><p>① \\\"몰\\\"은 제9조와 같은 구매신청에 대하여 다음 각호에 해당하지 않는 한 승낙합니다.<br>- 신청 내용에 허위, 기재누락, 오기가 있는 경우<br>- 미성년자가 담배, 주류등 청소년보호법에서 금지하는 재화 및 용역을 구매하는 경우<br>- 기타 구매신청에 승낙하는 것이 \\\"몰\\\" 기술상 현저히 지장이 있다고 판단하는 경우<br>- \\\"몰\\\"이 판단하여 판매가 부적합 하다고 판단되는 구매자의 경우<br>- \\\"몰\\\"이 준비한 수량이상으로 구매하여 재화 또는 용역을 제공하지 못한다고 판단하는 경우<br>② \\\"몰\\\"의 승낙이 제12조제1항의 수신확인통지형태로 이용자에게 도달한 시점에 계약이 성립한 것으로 봅니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제11조(지급방법)</span></p><br><p>몰\\\"에서 구매한 재화 또는 용역에 대한 대금지급방법은 다음 각호의 하나로 할 수 있습니다.<br>- 신용카드결제<br>- 계좌이체<br>- 무통장입금<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제12조(수신확인통지·구매신청 변경 및 취소)</span></p><br><p>① \\\"몰\\\"은 이용자의 구매신청이 있는 경우 이용자에게 수신확인통지를 합니다.<br>② 수신확인통지를 받은 이용자는 의사표시의 불일치등이 있는 경우에는 수신확인통지를 받은 후 즉시 구매신청 변경 및 취소를 요청할 수 있습니다.<br>③ \\\"몰\\\"은 배송전 이용자의 구매신청 변경 및 취소 요청이 있는 때에는 지체없이 그 요청에 따라 처리합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제13조(배송)</span></p><br><p>① \\\"몰\\\"은 이용자와 재화등의 공급시기에 관하여 별도의 약정이 없는 이상, 이용자가 청약을 한 날부터 7일 이내에 재화 등을 배송할 수 있도록 주문제작, 포장 등 기타의 필요한 조치를 취합니다.&nbsp;<br>다만, \\\"몰\\\"이 이미 재화 등의 대금의 전부 또는 일부를 받은 경우에는 대금의 전부 또는 일부를 받은 날부터 3영업일 이내에 조치를 취합니다.&nbsp;<br>이때 \\\"몰\\\"은 이용자가 재화등의 공급 절차 및 진행 사항을 확인할 수 있도록 적절한 조치를 합니다.<br>② \\\"몰\\\"은 이용자가 구매한 재화에 대해 배송수단, 수단별 배송비용 부담자, 수단별 배송기간 등을 명시합니다. 만약 \\\"몰\\\"이 약정 배송기간을 초과한 경우에는 그로 인한 이용자의 손해를 배상 하여야 합니다. 다만 \\\"몰\\\"이 고의·과실이 없음을 입증한 경우에는 그러하지 아니합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제14조(환급, 반품 및 교환)</span></p><br><p>① \\\"몰\\\"은 이용자가 구매신청한 재화 또는 용역이 품절등의 사유로 재화의 인도 또는 용역의 제공을 할 수 없을 때에는 지체없이 그 사유를 이용자에게 통지하고, 사전에 재화 또는 용역의 대금을 받은 경우에는 대금을 받은 날부터 영업3일이내에, 그렇지 않은 경우에는 그 사유발생일로부터 영업3일이내에 계약해제 및 환급절차를 취합니다.<br>② 다음 각호의 경우에는 \\\"몰\\\"은 배송된 재화일지라도 재화를 반품받은 다음 영업3일 이내에 이용자의 요구에 따라 즉시 환급, 반품 및 교환 조치를 합니다. 다만 그 요구기한은 배송된 날로부터 20일 이내로 합니다.<br>- 배송된 재화가 주문내용과 상이하거나 \\\"몰\\\"이 제공한 정보와 상이할 경우<br>- 배송된 재화가 파손, 손상되었거나 오염되었을 경우<br>- 재화가 광고에 표시된 배송기간보다 늦게 배송된 경우<br>- 방문판매등에관한법률 제18조에 의하여 광고에 표시하여야 할 사항을 표시하지 아니한 상태에서 이용자의 청약이 이루어진 경우<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제15조(개인정보보호)</span></p><br><p>① \\\"몰\\\"은 이용자의 정보수집시 구매계약 이행에 필요한 최소한의 정보를 수집합니다.<br>회원가입시 필수항목으로 지정된 정보 이외의 사항은 선택사항으로 합니다.<br>② \\\"몰\\\"이 이용자의 개인식별이 가능한 개인정보를 수집하는 때에는 반드시 당해 이용자의 동의를 받습니다.<br>③ 제공된 개인정보는 당해 이용자의 동의없이 목적외의 이용이나 제3자에게 제공할 수 없으며, 이에 대한 모든 책임은 \\\"몰\\\"이 집니다. 다만, 다음의 경우에는 예외로 합니다.<br>- 배송업무상 배송업체에게 배송에 필요한 최소한의 이용자의 정보(성명, 주소, 전화번호)를 알려주는 경우<br>- 통계작성, 학술연구 또는 시장조사를 위하여 필요한 경우로서 특정 개인을 식별할 수 없는 형태로 제공하는 경우<br>④ \\\"몰\\\"이 제2항과 제3항에 의해 이용자의 동의를 받아야 하는 경우에는 개인정보관리 책임자의 신원(소속, 성명 및 전화번호 기타 연락처), 정보의 수집목적 및 이용목적, 제3자에 대한 정보제공 관련사항(제공받는자, 제공목적 및 제공할 정보의 내용)등 정보통신망이용촉진등에관한법률 제16조제3항이 규정한 사항을 미리 명시하거나 고지해야 하며 이용자는 언제든지 이 동의를 철회할 수 있습니다.<br>⑤ 이용자는 언제든지 \\\"몰\\\"이 가지고 있는 자신의 개인정보에 대해 열람 및 오류정정을 요구할 수 있으며 \\\"몰\\\"은 이에 대해 지체없이 필요한 조치를 취할 의무를 집니다. 이용자가 오류의 정정을 요구한 경우에는 \\\"몰\\\"은 그 오류를 정정할 때까지 당해 개인정보를 이용하지 않습니다.<br>⑥ \\\"몰\\\"은 개인정보 보호를 위하여 관리자를 한정하여 그 수를 최소화하며 신용카드, 은행계좌 등을 포함한 이용자의 개인정보의 분실, 도난, 유출, 변조 등으로 인한 이용자의 손해에 대하여 모든 책임을 집니다.<br>⑦ \\\"몰\\\" 또는 그로부터 개인정보를 제공받은 제3자는 개인정보의 수집목적 또는 제공받은 목적을 달성한 때에는 당해 개인정보를 지체없이 파기합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제16조(\\\"몰\\\"의 의무)</span></p><br><p>① \\\"몰은 법령과 이 약관이 금지하거나 공서양속에 반하는 행위를 하지 않으며 이 약관이 정하는 바에 따라 지속적이고, 안정적으로 재화·용역을 제공하는 데 최선을 다하여야 합니다.<br>② \\\"몰\\\"은 이용자가 안전하게 인터넷 서비스를 이용할 수 있도록 이용자의 개인정보(신용정보 포함)보호를 위한 보안 시스템을 갖추어야 합니다.<br>③ \\\"몰\\\"이 상품이나 용역에 대하여 「표시·광고의공정화에관한법률」 제3조 소정의 부당한 표시·광고행위를 함으로써 이용자가 손해를 입은 때에는 이를 배상할 책임을 집니다.<br>④ \\\"몰\\\"은 이용자가 원하지 않는 영리목적의 광고성 전자우편을 발송하지 않습니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제17조( 회원의 ID 및 비밀번호에 대한 의무)</span></p><br><p>① 제15조의 경우를 제외한 ID와 비밀번호에 관한 관리책임은 회원에게 있습니다.<br>② 회원은 자신의 ID 및 비밀번호를 제3자에게 이용하게 해서는 안됩니다.<br>③ 회원이 자신의 ID 및 비밀번호를 도난당하거나 제3자가 사용하고 있음을 인지한 경우에는 바로 \\\"몰\\\"에 통보하고 \\\"몰\\\"의 안내가 있는 경우에는 그에 따라야 합니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제18조(이용자의 의무)</span></p><br><p>이용자는 다음 행위를 하여서는 안됩니다.<br>- 신청 또는 변경시 허위내용의 등록<br>- \\\"몰\\\"에 게시된 정보의 변경<br>- \\\"몰\\\"이 정한 정보 이외의 정보(컴퓨터 프로그램 등)의 송신 또는 게시<br>- \\\"몰\\\" 기타 제3자의 저작권 등 지적재산권에 대한 침해<br>- \\\"몰\\\" 기타 제3자의 명예를 손상시키거나 업무를 방해하는 행위<br>- 외설 또는 폭력적인 메시지·화상·음성 기타 공서양속에 반하는 정보를 몰에 공개 또는 게시하는 행위<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제19조(연결\\\"몰\\\"과 피연결\\\"몰\\\" 간의 관계)</span></p><br><p>① 상위 \\\"몰\\\"과 하위 \\\"몰\\\"이 하이퍼 링크(예: 하이퍼 링크의 대상에는 문자, 그림 및 동화상 등이 포함됨)방식 등으로 연결된 경우, 전자를 연결 \\\"몰\\\"(웹 사이트)이라고 하고 후자를 피연결 \\\"몰\\\"(웹사이트)이라고 합니다.<br>② 연결 \\\"몰\\\"은 피연결 \\\"몰\\\"이 독자적으로 제공하는 재화·용역에 의하여 이용자와 행하는 거래에 대해서 보증책임을지지 않는다는 뜻을 연결 \\\"몰\\\"의 사이트에서 명시한 경우에는 그 거래에 대한 보증책임을지지 않습니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제20조(저작권의 귀속 및 이용제한)</span></p><br><p>① \\\"몰\\\"이 작성한 저작물에 대한 저작권 기타 지적재산권은 \\\"몰\\\"에 귀속합니다.<br>② 이용자는 \\\"몰\\\"을 이용함으로써 얻은 정보를 \\\"몰\\\"의 사전 승낙없이 복제, 송신, 출판, 배포, 방송 기타 방법에 의하여 영리목적으로 이용하거나 제3자에게 이용하게 하여서는 안됩니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제21조(분쟁해결)</span></p><br><p>① \\\"몰\\\"은 이용자가 제기하는 정당한 의견이나 불만을 반영하고 그 피해를 보상처리하기 위하여 피해보상처리기구를 설치·운영합니다.<br>② \\\"몰\\\"은 이용자로부터 제출되는 불만사항 및 의견은 우선적으로 그 사항을 처리합니다. 다만, 신속한 처리가 곤란한 경우에는 이용자에게 그 사유와 처리일정을 즉시 통보해 드립니다.<br>③ \\\"몰\\\"과 이용자간에 발생한 분쟁은 전자거래기본법 제28조 및 동 시행령 제15조에 의하여 설치된 전자거래분쟁조정위원회의 조정에 따를 수 있습니다.<br></p><br><p style=\\\"letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\"><span style=\\\"color: rgb(0, 0, 0);\\\">제22조(재판권 및 준거법)</span></p><br><p>① \\\"몰\\\"과 이용자간에 발생한 전자거래 분쟁에 관한 소송은 민사소송법상의 관할법원에 제기합니다.<br>② \\\"몰\\\"과 이용자간에 제기된 전자거래 소송에는 한국법을 적용합니다.</p>'),(14,'Y','privacy_html_m','개인정보처리방침(MOBILE)','<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">총칙</p><br>\r\n			<p>\r\n			[쇼핑몰 기본정보::회사명]은 (이하 \\\'회사\\\'는) 고객님의 개인정보를 중요시하며, \\\"정보통신망 이용촉진 및 정보보호\\\"에 관한 법률을 준수하고 있습니다. <br>\r\n			회사는 개인정보취급방침을 통하여 고객님께서 제공하시는 개인정보가 어떠한 용도와 방식으로 이용되고 있으며, 개인정보보호를 위해 어떠한 조치가 취해지고 있는지 알려드립니다.<br>\r\n			회사는 개인정보취급방침을 개정하는 경우 웹사이트 공지사항(또는 개별공지)을 통하여 공지할 것입니다.<br>\r\n			본 방침은 : 2012 년 12 월 24 일 부터 시행됩니다.<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">수집하는 개인정보 항목</p><br>\r\n			<p>\r\n			회사는 회원가입, 상담, 서비스 신청 등등을 위해 아래와 같은 개인정보를 수집하고 있습니다.<br>\r\n			 - 수집항목 : 회원가입시 필수항목으로 지정된 정보, 서비스 이용기록, 접속 로그, 쿠키, 접속 IP 정보, 결제기록<br>\r\n			 - 개인정보 수집방법 : 홈페이지(회원가입)<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보의 수집 및 이용목적</p><br>\r\n			<p>\r\n			회사는 수집한 개인정보를 다음의 목적을 위해 활용합니다.<br>\r\n			 - 서비스 제공에 관한 계약 이행 및 서비스 제공에 따른 요금정산콘텐츠 제공, 구매 및 요금 결제, 물품배송 또는 청구지 등 발송, 금융거래 본인 인증 및 금융 서비스<br>\r\n			 - 회원 관리: 회원제 서비스 이용에 따른 본인확인, 개인 식별, 가입 의사 확인, 연령확인, 불만처리 등 민원처리<br>\r\n			 - 마케팅 및 광고에 활용 : 신규 서비스(제품) 개발 및 특화, 이벤트 등 광고성 정보 전달, 인구통계학적 특성에 따른 서비스 제공 및 광고 게재, 접속 빈도 파악 또는 회원의 서비스 이용에 대한 통계<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보의 보유 및 이용기간</p><br>\r\n			<p>\r\n			회사는 개인정보 수집 및 이용목적이 달성된 후에는 예외 없이 해당 정보를 지체 없이 파기합니다.<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보의 파기절차 및 방법</p><br>\r\n			<p>\r\n			회사는 원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체없이 파기합니다. 파기절차 및 방법은 다음과 같습니다.<br>\r\n			 - 파기절차 : 회원님이 회원가입 등을 위해 입력하신 정보는 목적이 달성된 후 별도의 DB로 옮겨져 내부 방침 및 기타 관련 법령에 의한 정보보호 사유에 따라(보유 및 이용기간 참조) 일정 기간 저장된 후 파기되어집니다.  별도 DB로 옮겨진 개인정보는 법률에 의한 경우가 아니고서는 보유되어지는 이외의 다른 목적으로 이용되지 않습니다.<br>\r\n			 - 파기방법 : 전자적 파일형태로 저장된 개인정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제합니다.<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보 제공</p><br>\r\n			<p>\r\n			회사는 이용자의 개인정보를 원칙적으로 외부에 제공하지 않습니다. 다만, 아래의 경우에는 예외로 합니다.<br>\r\n			 - 이용자들이 사전에 동의한 경우<br>\r\n			 - 법령의 규정에 의거하거나, 수사 목적으로 법령에 정해진 절차와 방법에 따라 수사기관의 요구가 있는 경우<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">수집한 개인정보의 위탁</p><br>\r\n			<p>\r\n			회사는 서비스 이행을 위해 아래와 같이 외부 전문업체에 위탁하여 운영하고 있습니다.<br>\r\n			 - 위탁 대상자 : (주)한국사이버결제,(주)스마일서브, 로젠 택배, 서비스 및 제품 판매 제휴 업체<br>\r\n			 - 위탁업무 내용 : 물품배송 또는 청구지 등 발송, 금융거래 본인 인증 및 금융 서비스, 회원관리 회원제 서비스 이용에 따른 본인확인, 웹사이트 및 시스템 관리<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">이용자 및 법정대리인의 권리와 그 행사방법</p><br>\r\n			<p>\r\n			이용자 및 법정 대리인은 언제든지 등록되어 있는 자신 혹은 당해 만 14세 미만 아동의 개인정보를 조회하거나 수정할 수 있으며 가입해지를 요청할 수도 있습니다. 이용자 혹은 만 14세 미만 아동의 개인정보 조회,수정을 위해서는\\\'개인정보변경\\\'(또는 \\\'회원정보수정\\\' 등)을 가입해지(동의철회)를 위해서는 \\\"회원탈퇴\\\"를 클릭하여 본인 확인 절차를 거치신 후 직접 열람, 정정 또는 탈퇴가 가능합니다. 혹은, 개인정보관리책임자에게 서면, 전화 또는 이메일로 연락하시면 지체없이 조치하겠습니다. 귀하가 개인정보의 오류에 대한 정정을 요청하신 경우에는 정정을 완료하기 전까지 당해 개인정보를 이용 또는 제공하지 않습니다. 또한 잘못된 개인정보를 제3자에게 이미 제공한 경우에는 정정 처리결과를 제3자에게 지체없이 통지하여 정정이이루어지도록 하겠습니다. 회사는 이용자 혹은 법정 대리인의 요청에 의해 해지 또는 삭제된 개인정보는 \\\"회사가 수집하는 개인정보의 보유 및 이용기간\\\"에 명시된 바에 따라 처리하고 그 외의용도로 열람 또는 이용할 수 없도록 처리하고 있습니다.\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보 자동수집 장치의 설치, 운영 및 그 거부에 관한 사항</p><br>\r\n			<p>\r\n			회사는 귀하의 정보를 수시로 저장하고 찾아내는 \\\'쿠키(cookie)\\\' 등을 운용합니다.<br>\r\n			 쿠키란 회사의 웹사이트를 운영하는데 이용되는 서버가 귀하의 브라우저에 보내는 아주 작은 텍스트 파일로서 귀하의 컴퓨터 하드디스크에 저장됩니다.<br>\r\n			 회사는 다음과 같은 목적을 위해 쿠키를 사용합니다.<br>\r\n			 - 쿠키 등 사용 목적<br>\r\n			 - 회원과 비회원의 접속 빈도나 방문 시간 등을 분석, 이용자의 취향과 관심분야를 파악 및 자취 추적, 각종 이벤트 참여 정도 및 방문 회수 파악 등을 통한 타겟 마케팅 및  개인 맞춤 서비스 제공<br>\r\n			 귀하는 쿠키 설치에 대한 선택권을 가지고 있습니다. 따라서, 귀하는 웹브라우저에서 옵션을 설정함으로써 모든 쿠키를 허용하거나, 쿠키가 저장될 때마다 확인을 거치거나 아니면 모든 쿠키의 저장을 거부할 수도 있습니다.<br>\r\n			 - 쿠키 설정 거부 방법 예: 쿠키 설정을 거부하는 방법으로는 회원님이 사용하시는 웹 브라우저의 옵션을 선택함으로서, 쿠키를 허용하거나 쿠키를 저장할 때마다 확인을 거치거나 쿠키의 저장을 거부할 수 있습니다.<br>\r\n			 설정방법 예(인터넷 익스플로어의 경우) : 웹 브라우저 상단의 도구 > 인터넷 옵션 > 개인정보<br>\r\n			 단, 귀하께서 쿠키 설치를 거부하였을 경우 서비스 제공에 어려움이 있을 수 있습니다.<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보에 관한 민원서비스</p><br>\r\n			<p>\r\n			회사는 고객의 개인정보를 보호하고 개인정보와 관련한 불만을 처리하기 위하여 아래와 같이 관련 부서 및 개인정보관리책임자를 지정하고 있습니다.<br><br>\r\n			<!-- 치환자 -->\r\n			 ▶ 고객서비스담당 부서 : [쇼핑몰 기본정보::개인정보관리책임자]<br>\r\n			 ▶ 전화번호 : [쇼핑몰 기본정보::고객센터 대표번호]<br>\r\n			 ▶ 이메일 : [쇼핑몰 기본정보::대표 이메일]<br><br>\r\n\r\n			 귀하께서는 회사의 서비스를 이용하시며 발생하는 모든 개인정보보호 관련 민원을 개인정보관리책임자 혹은 담당부서로 신고하실 수 있습니다.<br>\r\n			 회사는 이용자들의 신고사항에 대해 신속하게 충분한 답변을 드릴 것입니다.<br>\r\n			 기타 개인정보침해에 대한 신고나 상담이 필요하신 경우에는 아래 기관에 문의하시기 바랍니다.<br><br>\r\n\r\n			 1.개인정보침해신고센터 (<a href=\\\"http://www.1336.or.kr/국번없이\\\">www.1336.or.kr/국번없이</a> 118)<br>\r\n			 2.정보보호마크인증위원회 (<a href=\\\"http://www.eprivacy.or.kr/02-580-0533~4)\\\">www.eprivacy.or.kr/02-580-0533~4)</a><br>\r\n			 3.대검찰청 인터넷범죄수사센터 (<a href=\\\"http://icic.sppo.go.kr/02-3480-3600)\\\">http://icic.sppo.go.kr/02-3480-3600)</a><br>\r\n			 4.경찰청 사이버테러대응센터 (<a href=\\\"http://www.ctrc.go.kr/02-392-0330)\\\">www.ctrc.go.kr/02-392-0330)</a><br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">비 회원 주문시 개인정보 취급방침</p><br>\r\n			<p>\r\n			회사는 고객님의 개인정보를 중요시하며, \\\"정보통신망 이용촉진 및 정보보호\\\"에 관한 법률을 준수하고 있습니다.<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보의 수집 및 이용목적</p><br>\r\n			<p>\r\n			회사는 수집한 개인정보를 다음의 목적을 위해 활용합니다.<br>\r\n			 - 서비스 제공에 관한 계약 이행 및 서비스 제공에 따른 요금정산, 콘텐츠 제공, 구매 및 요금 결제 물품배송 또는 청구지 등 발송, 금융거래 본인 인증 및 금융 서비스<br>\r\n			 - 회원 관리 : 회원제 서비스 이용에 따른 본인확인, 개인 식별, 불량회원의 부정 이용 방지와 비인가 사용 방지, 가입 의사 확인, 연령확인 불만처리 등 민원처리, 고지사항 전달<br>\r\n			 - 마케팅 및 광고에 활용 : 이벤트 등 광고성 정보 전달 , 접속 빈도 파악 또는 회원의 서비스 이용에 대한 통계<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보의 보유 및 이용기간</p><br>\r\n			<p>\r\n			원칙적으로, 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체 없이 파기합니다.<br>\r\n			 단, 관계법령의 규정에 의하여 보존할 필요가 있는 경우 회사는 아래와 같이 관계법령에서 정한 일정한 기간 동안 회원정보를 보관합니다.<br><br>\r\n			 \r\n			 보존 항목 : 회원가입시 필수입력으로 지정된 항목에 한함.<br>\r\n			 보존 근거 : 전자상거래등에서의 소비자보호에 관한 법률<br>\r\n			 보존 기간 : 5년<br><br>\r\n			 \r\n			 표시/광고에 관한 기록 : 6개월 (전자상거래등에서의 소비자보호에 관한 법률)<br>\r\n			 계약 또는 청약철회 등에 관한 기록 : 5년 (전자상거래등에서의 소비자보호에 관한 법률)<br>\r\n			 대금결제 및 재화 등의 공급에 관한 기록 : 5년 (전자상거래등에서의 소비자보호에 관한 법률)<br>\r\n			 소비자의 불만 또는 분쟁처리에 관한 기록 : 3년 (전자상거래등에서의 소비자보호에 관한 법률)<br>\r\n			 신용정보의 수집/처리 및 이용 등에 관한 기록 : 3년 (신용정보의 이용 및 보호에 관한 법률)<br>\r\n			</p><br>\r\n			<p style=\\\"color: rgb(0, 0, 0); letter-spacing: -0.5px; font-size: 15px; font-weight: 600;\\\">개인정보의 파기절차 및 방법</p><br>\r\n			<p>\r\n			회사는 원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체없이 파기합니다.<br>\r\n			 파기절차 및 방법은 다음과 같습니다.<br>\r\n			 - 파기절차 : 회원님이 회원가입 등을 위해 입력하신 정보는 목적이 달성된 후 별도의 DB로 옮겨져 내부 방침 및 기타 관련 법령에 의한 정보보호 사유에 따라(보유 및 이용기간 참조) 일정 기간 저장된 후 파기되어집니다. 별도 DB로 옮겨진 개인정보는 법률에 의한 경우가 아니고서는 보유되어지는 이외의 다른 목적으로 이용되지 않습니다.<br>\r\n			 - 파기방법 : 전자적 파일형태로 저장된 개인정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제합니다.<br>\r\n			\\\"몰\\\"과 이용자간에 제기된 전자거래 소송에는 한국법을 적용합니다.<br>\r\n			</p>'),(15,'Y','join_privacy','[필수] 개인정보수집 및 이용 동의(회원가입)','■ 수집하는 개인정보 항목\r\n회사는 회원가입, 상담, 서비스 신청 등등을 위해 아래와 같은 개인정보를 수집하고 있습니다.\r\nο 수집항목 : 이름 , 로그인ID , 비밀번호 , 자택 전화번호 , 자택 주소 , 휴대전화번호 , 주민등록번호\r\nο 개인정보 수집방법 : 홈페이지(회원가입) \r\n\r\n■ 개인정보의 수집 및 이용목적\r\n(1) 서비스 제공에 관한 계약 이행 및 서비스 제공에 따른 요금정산 \r\n서비스 및 콘텐츠 제공, 물품배송 또는 청구서 등 발송, 본인인증, 구매 및 요금 결제, 요금추심 \r\n(2) 회원관리 \r\n회원제 서비스 이용에 따른 본인확인, 개인식별, 불량회원의 부정이용 방지와 비인가 사용방지, 가입의사 확인, 가입 및 가입횟수 제한, 만14세 미만 아동의 개인정보 수집 시 법정대리인 동의여부 확인, 추후 법정대리인 본인확인, 분쟁조정을 위한 기록 보존, 불만처리 등 민원처리, 고지사항 전달 \r\n(3) 신규서비스 개발 및 마케팅, 광고 \r\n신규서비스(제품) 개발 및 특화, 인구통계학적 특성에 따른 서비스 제공 및 광고 게재, 접속빈도 파악, 회원의 서비스 이용에 대한 통계, 이벤트 등 광고성 정보 전달\r\n\r\n■ 개인정보의 보유 및 이용기간\r\n원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체 없이 파기합니다. 단, 다음의 정보에 대해서는 아래의 이유로 명시한 기간 동안 보존합니다. \r\n(1) 회사 내부방침에 의한 정보보유 사유 \r\n회원이 탈퇴한 경우에도 회사는 원활한 서비스의 제공 및 부정한 서비스의 이용을 방지하기 위하여 아래와 같이 회원정보를 보관합니다. \r\n이름, 주민등록번호(여권번호/외국인등록번호), 이메일 주소, 전화번호 \r\n- 보존이유 : 서비스 이용의 혼선방지, 분쟁해결 및 수사기관의 요청에 따른 협조 \r\n- 보존기간 : 1 년 \r\n부정/불량이용기록 (부정/불량이용자의 개인정보 포함) \r\n- 보존이유 : 서비스의 부정 및 불량 이용 방지 및 부정/불량이용자의 재가입 방지 \r\n- 보존기간 : 1 년 \r\n(2) 관련 법령에 의한 정보보유 사유 \r\n상법, 전자상거래 등에서의 소비자보호에 관한 법률 등 관계 법령의 규정에 의하여 보존할 필요가 있는 경우 회사는 관계 법령에서 정한 일정한 기간 동안 회원정보를 보관합니다. 이 경우 회사는 보관하는 정보를 그 보관의 목적으로만 이용하며 보존기간은 아래와 같습니다. \r\n계약 또는 청약철회 등에 관한 기록 \r\n- 보존이유 : 전자상거래 등에서의 소비자보호에 관한 법률 \r\n- 보존기간 : 5년 \r\n대금결제 및 재화 등의 공급에 관한 기록 \r\n- 보존이유 : 전자상거래 등에서의 소비자보호에 관한 법률 \r\n- 보존기간 : 5년 \r\n소비자의 불만 및 분쟁처리에 관한 기록 \r\n- 보존이유 : 전자상거래 등에서의 소비자보호에 관한 법률 \r\n- 보존기간 : 3년 \r\n로그기록 \r\n- 보존이유 : 통신비밀보호법 \r\n- 보존기간 : 3개월'),(234,'Y','deny_html_m','이메일무단수집거부(MOBILE)','본사이트에 게시된 이메일 주소가 전자우편 수집프로그램이나 그밖의 기술적 장치를 이용하여 무단으로 수집 되는 것을 거부하며, 이를 위반시 정보통신망법에 의해 형사처벌됨을 유념하시기 바랍니다.<br><p>\r\n정보통신망이용 및 정보보호 등에 관한법률(일부개정 2002.12.18 법률 제0697호)</p><br><br>\r\n\r\n<p style=\\\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\\\">제 50조의 2(전자우편주소의 무단수집행위 등 금지)</p><br>\r\n<p>• 누구든지 전자우편주소의 수집을 거부하는 의사가 명시된 인터넷 홈페이지에서 자동으로 전자우편주소를 수집하는 프로그램 그 밖의 기술적 장치를 이용하여 전자우편주소를 수집하여서는 아니된다.<br>\r\n• 누구든지 제1항의 규정을 위반하여 수집된 전자우편주소를 판매 유통하여서는 아니된다.<br>\r\n• 누구든지 제1항 및 제2항의 규정에 의하여 수집 판매 및 유통이 금지된 전자우편주소임을 알고 이를 정보전송에 이용하여서는 아니된다.</p><br><br>\r\n\r\n<p style=\\\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\\\">제 65조의 2(벌칙)다음각호의 1에 해당하는 자는 1천만원이하의 벌금에 처한다.</p><br>\r\n<p>• 제65조의 2(벌칙) 다음 각호의 1에 해당하는 자는 1천만원 이하의 벌금에 처한다.<br>\r\n• 제50조 제4항의 규정을 위반하여 기술적 조치를 한자<br>\r\n• 제50조 제6항의 규정을 위반하여 영리목적의 광고성 정보를 전송한 자<br>\r\n• 제50조 제2항의 규정을 위반하여 전자우편주소를 수집 · 판매 · 유통 또는 정보전송에 이용한자​</p>\r\n'),(409,'Y','join_thirdinfo','마케팅 활용을 위한 개인정보 제3자 제공 동의','- 제공받는 자 : (주) 000\r\n- 이용목적 : 마케팅을 위한 개인정보 이용\r\n- 제공항목 : 구매자정보, 상품 구매/취소/반품/교환/환불 정보, 수령인 정보\r\n- 보유/이용기간 : 회원탈퇴 후 5일까지'),(410,'Y','join_thirdinfo','공급사 상품 판매 및 배송을 위한 개인정보 제3자 제공 동의','- 제공받는 자 : 공급사 판매자\r\n- 이용목적 : 판매자와 구매자의 거래의 원활한 진행, 본인의사의확인, 고객 상담 및 불만처리, 상품과 경품 배송을 위한 배송지 확인 등\r\n- 제공항목 : 구매자 이름, 전화번호, ID, 휴대폰번호, 이메일주소, 상품 구매정보, 상품 수취인 정보(이름, 주소, 전화번호)\r\n- 보유/이용기간 : 배송완료 후 한달'),(407,'Y','join_optional','주문/결제 및 배송서비스','- 수집/이용목적 : 주문/결제 시 상품 배송\r\n- 수집항목 : 구매자정보, 상품 구매/취소/반품/교환/환불 정보, 수령인 정보\r\n- 보유/이용기간 : 회원탈퇴 후 5일까지'),(408,'Y','join_csinfo','고객 클래임 처리를 위한 CS 위탁','- 개인정보 처리위탁을 받는 자 : (주) 000\r\n- 처리위탁 업무내용 : 마케팅을 위한 개인정보 이용');
/*!40000 ALTER TABLE `smart_policy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_popup`
--

DROP TABLE IF EXISTS `smart_popup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_popup` (
  `p_uid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `p_img` varchar(30) DEFAULT NULL COMMENT '팝업이미지',
  `p_title` varchar(200) NOT NULL COMMENT '팝업 타이틀',
  `p_link` varchar(200) DEFAULT NULL COMMENT '팝업링크',
  `p_target` enum('_self','_blank') NOT NULL DEFAULT '_self' COMMENT '링크방식',
  `p_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '노출여부',
  `p_idx` int(8) unsigned NOT NULL COMMENT '팝업 순위',
  `p_sort_group` int(8) NOT NULL DEFAULT 100 COMMENT '팝업 우선순위 그룹',
  `p_sort_idx` decimal(8,1) NOT NULL COMMENT '팝업 우선순위그룹내 순위',
  `p_left` int(5) NOT NULL DEFAULT 0 COMMENT '팝업 위치 - 왼쪽으로부터',
  `p_top` int(5) NOT NULL DEFAULT 0 COMMENT '팝업 위치 - 위쪽으로부터',
  `p_mtop` int(5) NOT NULL DEFAULT 0 COMMENT '팝업 위치 - 위쪽으로부터(모바일)',
  `p_sdate` date NOT NULL COMMENT '시작일 - 00 시부터 시작',
  `p_edate` date NOT NULL COMMENT '종료일 - 23시 59분 59초까지',
  `p_rdate` datetime DEFAULT NULL COMMENT '팝업등록일',
  `p_none_limit` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '팝업무기한게재여부',
  `p_type` enum('A','P','M') NOT NULL DEFAULT 'A' COMMENT '노출구분(A:전체, P:PC, M:모바일)',
  `p_mode` enum('I','E') NOT NULL DEFAULT 'I' COMMENT '팝업모드(I: 이미지형, E: 에디터형)',
  `p_bgcolor` varchar(7) NOT NULL COMMENT '에디터 배경색',
  `p_content` text NOT NULL COMMENT '에디터 내용',
  `p_width` int(4) NOT NULL DEFAULT 350 COMMENT '에디터 가로크기',
  `p_height` int(4) NOT NULL DEFAULT 250 COMMENT '에디터 세로크기',
  PRIMARY KEY (`p_uid`),
  KEY `p_target` (`p_target`),
  KEY `p_view` (`p_view`),
  KEY `p_type` (`p_type`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='팝업관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_popup`
--

LOCK TABLES `smart_popup` WRITE;
/*!40000 ALTER TABLE `smart_popup` DISABLE KEYS */;
INSERT INTO `smart_popup` VALUES (12,'703879172.jpg','팝업 타이틀','/?pn=product.promotion_list','_self','Y',2,2,0.5,0,0,0,'0000-00-00','0000-00-00','2023-03-08 11:06:20','Y','A','I','#FFFFFF','',350,250),(9,'','반응형 쇼핑몰 솔루션 OPEN!','','_self','Y',1,1,0.5,0,0,0,'2023-03-01','2023-07-27','2021-01-26 10:31:21','N','A','E','#F5F5F5','<p style=\"text-align: left;\" align=\"left\"><span style=\"font-size: 12pt; color: rgb(0, 0, 0);\"><b>반응형 쇼핑몰 솔루션 OPEN!</b></span><span style=\"color: rgb(0, 0, 0); font-size: 12pt;\">&nbsp;</span></p><p style=\"text-align: left;\" align=\"left\"><span style=\"font-size: 10pt;\"><br></span></p><p style=\"text-align: left;\" align=\"left\"><span style=\"font-size: 10pt;\">배송상품과 티켓까지 판매 가능한</span></p><p style=\"text-align: left;\" align=\"left\"><span style=\"font-size: 10pt;\">FULL 반응형 쇼핑몰 솔루션이 오픈했습니다!</span></p><p style=\"text-align: left;\" align=\"left\"><br></p><p style=\"text-align: left;\" align=\"left\">사용자는 물론, 관리자까지 완전한 반응형으로 제작된</p><p style=\"text-align: left;\" align=\"left\">독립형 쇼핑몰 솔루션 하이센스 PLUS를 지금 만나보세요.</p>',350,250);
/*!40000 ALTER TABLE `smart_popup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product`
--

DROP TABLE IF EXISTS `smart_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product` (
  `p_code` varchar(20) NOT NULL DEFAULT '' COMMENT '상품코드 - 영숫자조합으로 15글자 적용, 예)A1234-B1234-C1234',
  `p_cuid` int(10) DEFAULT 0 COMMENT '카테고리 uid - depth가 3인 카테고리 uid 적용',
  `p_cpid` varchar(30) NOT NULL COMMENT '등록 기업회원아이디',
  `p_stock` int(10) NOT NULL DEFAULT 0 COMMENT '재고량',
  `p_salecnt` int(10) NOT NULL DEFAULT 0 COMMENT '판매수량',
  `p_price` int(10) NOT NULL DEFAULT 0 COMMENT '할인가 - 실판매가',
  `p_point_per` decimal(10,1) NOT NULL DEFAULT 0.0 COMMENT '적립율',
  `p_name` varchar(100) NOT NULL DEFAULT '' COMMENT '상품명',
  `p_subname` varchar(100) NOT NULL DEFAULT '' COMMENT '상품부제목',
  `p_view` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상품노출여부',
  `p_option_valid_chk` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '옵션 적합성 체크',
  `p_newview` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '신규상품 노출여부',
  `p_saleview` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '세일중상품',
  `p_bestview` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '베스트상품',
  `p_content` text DEFAULT NULL COMMENT '상품상세정보',
  `p_content_m` text NOT NULL COMMENT '상품상세정보(모바일)',
  `p_rdate` datetime DEFAULT NULL COMMENT '상품저장일시',
  `p_img_list` varchar(255) DEFAULT NULL COMMENT '이미지 - 목록, (100x100)',
  `p_img_list_over` varchar(255) DEFAULT NULL COMMENT '이미지 - 목록 오버 이미지 (620x620)',
  `p_img_b1` varchar(255) DEFAULT NULL COMMENT '이미지 - 큰이미지1 - 메인이미지(630x450) , 자동 thumbnail 100x100 적용',
  `p_img_b2` varchar(255) DEFAULT NULL COMMENT '이미지 - 큰이미지2 - 메인이미지(630x450) , 자동 thumbnail 100x100 적용',
  `p_img_b3` varchar(255) DEFAULT NULL COMMENT '이미지 - 큰이미지3 - 메인이미지(630x450) , 자동 thumbnail 100x100 적용',
  `p_img_b4` varchar(255) DEFAULT NULL COMMENT '이미지 - 큰이미지4 - 메인이미지(630x450) , 자동 thumbnail 100x100 적용',
  `p_img_b5` varchar(255) DEFAULT NULL COMMENT '이미지 - 큰이미지5 - 메인이미지(630x450) , 자동 thumbnail 100x100 적용',
  `p_img_list2` varchar(255) NOT NULL COMMENT '상품 이미지 목록2',
  `p_img_list_square` varchar(255) NOT NULL,
  `p_coupon` varchar(100) DEFAULT NULL COMMENT '상품쿠폰, 예)배송비할인쿠폰|2500',
  `p_idx` int(8) unsigned NOT NULL DEFAULT 0 COMMENT '상품등록순위',
  `p_option_type_chk` enum('nooption','1depth','2depth','3depth') NOT NULL DEFAULT 'nooption' COMMENT '옵션 사용여부 및 depth',
  `p_shorten_url` varchar(50) DEFAULT NULL COMMENT '고유한 짧은 url',
  `p_orgin` varchar(50) NOT NULL DEFAULT '' COMMENT '원산지',
  `p_maker` varchar(60) NOT NULL DEFAULT '' COMMENT '제조사',
  `p_shoppingPay_use` enum('Y','N','F','P') NOT NULL DEFAULT 'N' COMMENT '상품 배송비정책 사용여부',
  `p_shoppingPay` int(11) NOT NULL DEFAULT 0 COMMENT '배송비',
  `p_shoppingPayFree` int(11) NOT NULL DEFAULT 0 COMMENT '무료배송가',
  `p_shoppingPayPdPrice` int(11) NOT NULL DEFAULT 0 COMMENT '상품별 배송비 - 상품별 기본배송비',
  `p_shoppingPayPfPrice` int(11) NOT NULL DEFAULT 0 COMMENT '상품별 배송비 - 무료배송비',
  `p_commission_type` varchar(50) NOT NULL DEFAULT '공급가' COMMENT '입점업체 정산방식 (수수료/공급가)',
  `p_sPersent` decimal(10,2) NOT NULL COMMENT '수수료(예:1 - 1%)',
  `p_sPrice` int(10) NOT NULL DEFAULT 0 COMMENT '공급가격',
  `p_icon` varchar(50) NOT NULL DEFAULT '' COMMENT '상품 아이콘 uid',
  `p_screenPrice` int(11) NOT NULL DEFAULT 0 COMMENT '기존가격',
  `p_delivery_info` varchar(200) NOT NULL DEFAULT '' COMMENT '배송정보',
  `p_relation` text NOT NULL COMMENT '관련상품',
  `p_relation_type` enum('none','category','manual') NOT NULL DEFAULT 'none' COMMENT '관련상품 노출 형식 (none:비노출, category:동일카테고리 상품 랜덤 노출, manual:선택한 상품 노출)',
  `p_naver_switch` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '네이버 EP 상품별 적용',
  `p_daum_switch` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '다음 EP 상품별 적용',
  `p_mdate` datetime DEFAULT NULL COMMENT '상품수정일시',
  `p_hashtag` text NOT NULL COMMENT '해시태그 목록',
  `p_hashtag_shuffle` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '해시태그 셔플여부 (Y: 적용, N: 미적용)',
  `npay_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'NPay 결제 사용유무',
  `p_vat` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '과세여부 - Y:과세, N:면세',
  `p_sort_group` int(8) NOT NULL DEFAULT 100 COMMENT '우선순위 그룹',
  `p_sort_idx` decimal(8,1) NOT NULL DEFAULT 0.0 COMMENT '우선순위 그룹내 순위',
  `p_use_content` enum('Y','N') NOT NULL COMMENT 'PC/MOBILE 상세설명 함께 사용(Y:함께사용, N:모바일따로사용))',
  `p_brand` int(10) NOT NULL COMMENT '상품 브랜드',
  `p_groupset_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '등급별 혜택 적용여부',
  `p_free_delivery_event_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '무료배송이벤트 일시 해당 상품 적용여부',
  `p_option1_type` enum('normal','size','color') NOT NULL DEFAULT 'normal' COMMENT '1차옵션타입 - normal:일반형, size:사이즈형, color:색상형',
  `p_option2_type` enum('normal','size','color') NOT NULL DEFAULT 'normal' COMMENT '2차옵션타입 - normal:일반형, size:사이즈형, color:색상형',
  `p_option3_type` enum('normal','size','color') NOT NULL DEFAULT 'normal' COMMENT '3차옵션타입 - normal:일반형, size:사이즈형, color:색상형',
  `p_soldout_chk` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상품품절체크',
  `p_option1_title` varchar(100) NOT NULL COMMENT '1차 옵션 타이틀',
  `p_option2_title` varchar(100) NOT NULL COMMENT '2차 옵션 타이틀',
  `p_option3_title` varchar(100) NOT NULL COMMENT '3차 옵션 타이틀',
  `p_sale_type` enum('T','A') NOT NULL DEFAULT 'A' COMMENT '판매유형 (기간판매:T, 상시판매:A)',
  `p_sale_sdate` date DEFAULT NULL COMMENT '기간판매일 경우 판매시작일 (date)',
  `p_sale_edate` date DEFAULT NULL COMMENT '기간판매일 경우 판매종료일 (date)',
  `p_sale_sdateh` int(11) NOT NULL COMMENT '판매일자 상세시간',
  `p_sale_sdatem` int(11) NOT NULL COMMENT '판매일자 상세시간',
  `p_sale_edateh` int(11) NOT NULL COMMENT '판매일자 상세시간',
  `p_sale_edatem` int(11) NOT NULL COMMENT '판매일자 상세시간',
  `p_expire_type` enum('','day','date') NOT NULL DEFAULT '' COMMENT '티켓유효기간 타입('''': 없음, day:만료일지정, date: 만료날짜 지정)',
  `p_expire_day` int(11) NOT NULL DEFAULT 0 COMMENT '티켓유효기간이 만료일 지정일 경우 만료일',
  `p_expire_date` date DEFAULT NULL COMMENT '티켓유효기간이 만료날짜 지정일 경우 만료날짜',
  `p_type` enum('delivery','ticket') NOT NULL DEFAULT 'delivery' COMMENT '상품타입 (delivery: 배송, ticket:티켓)',
  `p_dateoption_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상품당 달력옵션 사용여부(Y:사용,N:미사용)',
  `p_dateoption_date_type` enum('day','date') NOT NULL DEFAULT 'day' COMMENT '상품당 선택날짜 지정 타입(day:일자지정, date:날짜지정)',
  `p_dateoption_sday` int(11) NOT NULL DEFAULT 0 COMMENT '상품당 날짜선택 가능일(~일 이후 부터 ) (p_dateoption_date_type == day 일경우)',
  `p_dateoption_eday` int(11) NOT NULL DEFAULT 0 COMMENT '상품당 날짜선택 종료일 (~일 이전 까지) (p_dateoption_date_type == day 일경우)',
  `p_dateoption_sdate` date DEFAULT NULL COMMENT '상품당 날짜선택 가능날짜 (p_dateoption_date_type == date 일경우)',
  `p_dateoption_edate` date DEFAULT NULL COMMENT '상품당 날짜선택 종료날짜 (p_dateoption_date_type == date 일경우)',
  `p_dateoption_stime` varchar(5) DEFAULT NULL COMMENT '상품당 당일선택에 따른 가능 시간(몇시부터~)',
  `p_dateoption_etime` varchar(5) DEFAULT NULL COMMENT '상품당 당일선택에 따른 가능 시간(~몇시까지)',
  `p_dateoption_ex_week` set('1','2','3','4','5','6','7') DEFAULT NULL COMMENT '상품당 날짜선택 불가 요일(일(1)~토(7))',
  `p_dateoption_ex_date` text DEFAULT NULL COMMENT '상품당 날짜선택 불가 날짜(다수개 지정)',
  `p_buy_limit` int(11) DEFAULT 0 COMMENT '1회 최대 구매개수 ',
  `p_duplicate_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '상품 중복사용여부(Y:가능, N:불가능) - 기본가능',
  `p_com_juso` varchar(255) NOT NULL COMMENT '상품지도: 주소',
  `p_com_mapx` varchar(30) NOT NULL COMMENT '상품지도: X좌표',
  `p_com_mapy` varchar(30) NOT NULL COMMENT '상품지도: Y좌표',
  `p_apply_mapxy` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '지도표기 좌표로 설정(Y:좌표 기준, N:주소기준)',
  `p_com_tel` varchar(30) DEFAULT NULL COMMENT '위치정보에 함께 표기될 전화번호',
  `p_com_locname` varchar(50) DEFAULT NULL COMMENT '위치정보에 함께 표기될 장소이름',
  `p_time_sale` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '타임세일 적용 여부 (Y:적용, N:미적용)',
  `p_time_sale_sdate` date DEFAULT NULL COMMENT '타임세일 시작일 ',
  `p_time_sale_sclock` time DEFAULT NULL COMMENT '타임세일 시작시간',
  `p_time_sale_edate` date DEFAULT NULL COMMENT '타임세일 종료일',
  `p_time_sale_eclock` time DEFAULT NULL COMMENT '타임세일 종료시간',
  `p_com_time` text DEFAULT NULL COMMENT '위치정보 운영시간 ',
  PRIMARY KEY (`p_code`),
  KEY `p_cuid` (`p_cuid`),
  KEY `p_cpid` (`p_cpid`),
  KEY `p_view` (`p_view`),
  KEY `p_newview` (`p_newview`),
  KEY `p_saleview` (`p_saleview`),
  KEY `p_bestview` (`p_bestview`),
  KEY `p_option_type_chk` (`p_option_type_chk`),
  KEY `p_shoppingPay_use` (`p_shoppingPay_use`),
  KEY `p_naver_switch` (`p_naver_switch`),
  KEY `p_daum_switch` (`p_daum_switch`),
  KEY `npay_use` (`npay_use`),
  KEY `p_hashtag_shuffle` (`p_hashtag_shuffle`),
  KEY `p_vat` (`p_vat`),
  KEY `p_sort_group` (`p_sort_group`,`p_sort_idx`),
  KEY `p_brand` (`p_brand`),
  KEY `p_option_valid_chk` (`p_option_valid_chk`),
  KEY `p_soldout_chk` (`p_soldout_chk`),
  KEY `p_dateoption_use` (`p_dateoption_use`),
  KEY `p_type` (`p_type`),
  KEY `p_dateoption_date_type` (`p_dateoption_date_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product`
--

LOCK TABLES `smart_product` WRITE;
/*!40000 ALTER TABLE `smart_product` DISABLE KEYS */;
INSERT INTO `smart_product` VALUES ('A1293-C2219-A8817',144,'submaster',108,4,23,1.0,'호두 파운드 케이크 - 옵션 1차','메이플 시럽의 달콤함을 더했어요','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"베리베리 아이스크림\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"베리베리 아이스크림\\\"><br style=\\\"clear:both;\\\">','2023-03-18 16:22:54','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','오픈 할인 쿠폰|||0|0',32,'1depth','http://goo.gl/dm5Cj3','한국','뷰티홀릭','F',0,0,0,0,'공급가',0.00,0,'4,3',27,'','','category','Y','Y','2023-03-23 14:28:39','케이크,빵,디저트','','Y','Y',100,31.0,'Y',24,'Y','N','normal','color','normal','N','세트','색상','사이즈','A','2023-02-23','2024-02-23',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-07','00:00:00','2024-03-07','00:00:00',NULL),('H7822-Q6889-R6001',217,'submaster',998,2,10015,0.0,'촉촉하고 고소한 견과 컵케이크 - 옵션 2차','부드러운 초코크림과 고소한 견과류를 올린 최고의 간식','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼레몬 파운드케익 컬러 옵션\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼레몬 파운드케익 컬러 옵션\\\"><br style=\\\"clear:both;\\\">','2023-03-14 16:24:07','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',16,'2depth','','블렌딩','뷰티홀릭','N',0,0,0,0,'수수료',10.00,0,'',0,'cj대한통운','','none','N','N','2023-03-29 17:40:58','컵케이크,디저트','','Y','Y',100,15.5,'Y',24,'N','N','color','size','size','N','색상','사이즈','','A','2023-03-07','2024-03-07',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-07','00:00:00','2024-03-07','00:00:00',NULL),('A3069-L9262-I4831',500,'submaster',107,5,10025,1.0,'쇼콜라 조각케이크','블랙초코 시트에 진한 가나슈 초콜릿을 샌드한 케이크','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"컵케이크 선물세트 4p\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"컵케이크 선물세트 4p\\\"><br style=\\\"clear:both;\\\">','2023-03-18 16:23:53','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',23,'nooption','http://goo.gl/dm5Cj3','한국','다과상사','N',0,0,0,0,'공급가',0.00,0,'4',0,'cj대한통운','','category','Y','Y','2023-03-29 17:38:39','','','Y','Y',100,22.0,'Y',24,'N','N','normal','normal','normal','N','','','','A','2023-03-09','2024-03-09',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('A1404-E8574-G1535',447,'submaster',997,6,5,1.0,'오리지널 & 생크림 도넛','','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"수제 견과 초콜릿 세트 옵션\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"수제 견과 초콜릿 세트 옵션\\\"><br style=\\\"clear:both;\\\">','2023-03-18 16:23:54','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',34,'nooption','http://goo.gl/dm5Cj3','한국','뷰티홀릭','Y',5000,0,0,0,'공급가',0.00,0,'',0,'','','category','Y','Y','2023-03-29 17:40:04','도넛,디저트','','Y','Y',100,33.0,'Y',34,'Y','N','normal','color','size','N','','','','A','2023-03-07','2024-03-07',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-07','00:00:00','2024-03-07','00:00:00',NULL),('A2668-K4581-X3347',500,'submaster',56,56,10011,5.5,'베이컨 토마토 샌드위치 - 옵션 3차, 추가 옵션','베이컨, 토마토, 양상추를 곁들인 건강한 웰빙푸드입니다.','Y','Y','N','Y','N','<p><img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\"></p><p><br></p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\"></p><p><br></p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\"></p><p><br></p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\"></p><p><br></p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\"></p><p><br></p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p>','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\"><br style=\\\"clear:both;\\\">','2023-03-18 16:23:54','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','오픈 할인 쿠폰|price|1000|2.0|3000',10,'3depth','http://goo.gl/dm5Cj3','한국','뷰티홀릭','N',0,0,0,0,'공급가',0.00,0,'4',12000,'cj대한통운','Z7913-S9943-R3804|M5600-U3831-O7938|B5485-Q8863-C2763|N7005-W5489-B0129|S3629-N2275-E9290|X5785-V0661-C5698|X5758-P1862-F7949|X8000-P0254-T3646','manual','Y','Y','2023-03-30 11:16:46','케이크,디저트','','Y','Y',100,9.5,'Y',24,'Y','Y','normal','normal','size','N','','','','T','2023-03-27','2024-03-27',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,4,'Y','광주 서구 풍암2로 36 (풍암동, 주은모아아파트)','126.880611979919','35.1235447407777','','',NULL,'N','2023-03-29','19:07:00','2023-03-30','19:07:00',NULL),('A2939-W4908-C9360',1,'submaster',107,5,10025,1.0,'라즈베리 듬뿍 미니 브라우니','한입 크기의 브라우니로 간편하게 즐길 수 있습니다','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"한입 쏙 달달 케이크\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"한입 쏙 달달 케이크\"><br style=\"clear:both;\">','2023-03-18 16:23:54','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',24,'nooption','http://goo.gl/dm5Cj3','한국','뷰티홀릭','Y',3000,0,0,0,'공급가',0.00,0,'4,3',0,'cj대한통운','','none','Y','Y','2023-03-29 17:39:17','','','Y','Y',100,23.0,'Y',24,'Y','N','normal','normal','normal','N','','','','A','2023-03-07','2024-03-07',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-07','00:00:00','2024-03-07','00:00:00',NULL),('A3360-Z7477-M6326',451,'submaster',97,15,32,1.0,'오렌지 크림 도넛','','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"아기자기 달콤도넛\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"아기자기 달콤도넛\\\"><br style=\\\"clear:both;\\\">','2023-03-18 16:23:54','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',25,'nooption','http://goo.gl/dm5Cj3','한국','뷰티홀릭','Y',5000,0,0,0,'공급가',0.00,0,'4',45,'','','category','Y','Y','2023-03-29 17:39:07','','','Y','Y',100,24.0,'Y',24,'N','N','normal','normal','normal','N','','','','A','2023-03-09','2024-03-09',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('A0851-Y4325-F5506',447,'submaster',108,4,4,1.0,'유기농 통밀빵','다이어트에 좋은 100% 통밀빵','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"딸기 크림 컵케익 머핀\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"딸기 크림 컵케익 머핀\"><br style=\"clear:both;\">','2023-03-18 16:23:54','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',26,'nooption','http://goo.gl/dm5Cj3','한국','뷰티홀릭','Y',3000,0,0,0,'공급가',0.00,0,'',0,'cj대한통운','','none','Y','Y','2023-03-16 16:06:19','','','Y','Y',100,25.0,'Y',24,'Y','Y','normal','normal','normal','N','','','','A','2023-03-07','2024-03-07',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-07','00:00:00','2024-03-07','00:00:00',NULL),('A1561-R7770-B0752',2,'submaster',108,4,12,1.0,'땅콩 초코 도넛','','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"캬라멜 듬뿍 쿠키\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"캬라멜 듬뿍 쿠키\"><br style=\"clear:both;\">','2023-03-18 16:23:54','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',27,'nooption','http://goo.gl/dm5Cj3','한국','뷰티홀릭','Y',3000,0,0,0,'공급가',0.00,0,'4',0,'cj대한통운','','none','Y','Y','2023-03-29 17:38:51','','','Y','Y',100,26.0,'Y',24,'Y','N','normal','normal','normal','N','','','','A','2023-03-09','2024-03-09',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('A1853-T6120-V7609',447,'submaster',104,8,12,1.0,'햄치즈 샌드위치 - 옵션 2차, 추가 옵션','기본에 충실한 햄치즈 샌드위치','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"알록달록 과일 데코 쿠키\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"알록달록 과일 데코 쿠키\"><br style=\"clear:both;\">','2023-03-18 16:23:55','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',28,'2depth','http://goo.gl/dm5Cj3','한국','뷰티홀릭','P',0,0,1000,5000,'공급가',0.00,0,'4',0,'cj대한통운','','category','Y','Y','2023-03-29 17:40:23','샌드위치,간식','','Y','Y',100,27.0,'Y',24,'Y','Y','normal','normal','normal','Y','','','','A','2023-01-05','2024-01-05',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-02','00:00:00','2024-03-02','00:00:00',NULL),('A2476-K3606-A0582',493,'submaster',108,4,7,1.0,'후르츠  컵케이크 - 옵션 2차','후르츠와 딸기크림을 올린 컵케이크','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코 앤 바닐라 아이스크림\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"초코 앤 바닐라 아이스크림\"><br style=\"clear:both;\">','2023-03-18 16:23:55','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',29,'2depth','http://goo.gl/dm5Cj3','한국','뷰티홀릭','F',0,0,0,0,'공급가',0.00,0,'4,3',0,'cj대한통운','','category','Y','Y','2023-03-29 17:40:14','','','Y','Y',100,28.0,'Y',26,'Y','N','normal','normal','normal','N','','','','A','2023-03-09','2024-03-09',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('A0344-N7288-E3075',147,'submaster',1000,4,20,1.0,'미니 초콜릿 컵케이크','','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"딸기 밀크 마카롱\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"딸기 밀크 마카롱\"><br style=\"clear:both;\">','2023-03-18 16:23:56','auto_s_2609399598.jpg','','auto_main_2609399598.jpg','','','','','auto_s_2609399598.jpg','auto_s_2609399598.jpg','||0|0|0',30,'nooption','http://goo.gl/dm5Cj3','한국','뷰티홀릭','N',0,0,0,0,'공급가',0.00,0,'4,3',21,'','','none','Y','Y','2023-03-30 14:25:56','','','Y','Y',100,29.0,'Y',30,'N','N','color','normal','normal','N','','','','A','2023-03-09','2024-03-09',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('A0369-A2723-E5596',203,'submaster',102,10,20,1.0,'티라미수 마카롱 - 옵션 1차, 추가 옵션','커피와 어울리는 마카롱','Y','Y','N','Y','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"무지개 머핀\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"무지개 머핀\"><br style=\"clear:both;\">','2023-03-18 16:23:56','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',31,'1depth','http://goo.gl/dm5Cj3','한국','뷰티홀릭','Y',3000,0,0,0,'공급가',0.00,0,'3',30,'','','category','Y','Y','2023-03-29 17:40:37','','','Y','Y',100,30.0,'Y',0,'Y','N','color','normal','normal','N','','','','A','2023-03-07','2024-03-07',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-07','00:00:00','2024-03-07','00:00:00',NULL),('W0678-D3642-R9462',1,'submaster',0,15,10002,0.0,'생크림을 품은 딸기팬케이크','','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"포송포송 구름같은 미니 마카롱\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"포송포송 구름같은 미니 마카롱\"><br style=\"clear:both;\">','2023-03-15 14:46:18','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0.0|0',22,'nooption','','','','N',0,0,0,0,'수수료',10.00,0,'',0,'','','category','N','N','2023-03-21 20:41:23','','','Y','Y',100,21.5,'Y',34,'Y','N','normal','normal','normal','Y','','','','A','2023-02-22','2024-02-22',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('U8905-L6749-Y3508',494,'submaster',83,16,10003,0.0,'상큼함을 담은 아보카도','허브향을 머금은 상큼한 아보카도 디저트로 마무리 해보세요','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코 무스무스 생딸기 케이크\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코 무스무스 생딸기 케이크\\\"><br style=\\\"clear:both;\\\">','2023-03-15 14:47:20','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',21,'nooption','','','','N',0,0,0,0,'수수료',10.00,0,'',0,'','','category','N','N','2023-03-29 17:41:19','','','Y','Y',100,20.5,'Y',0,'N','N','normal','normal','normal','N','','','','A','2022-12-30','2023-12-30',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-07','00:00:00','2024-03-07','00:00:00',NULL),('C6364-U7256-J4618',0,'submaster',34,65,10002,0.0,'꾸덕 화이트&초코 마카롱','','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"입에서 살살녹는 마카롱\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"입에서 살살녹는 마카롱\"><br style=\"clear:both;\">','2023-03-15 14:51:13','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',20,'nooption','','','','N',0,0,0,0,'수수료',10.00,0,'',0,'','','none','N','N','2023-03-29 17:41:32','','','Y','Y',100,19.5,'Y',27,'Y','N','normal','normal','normal','N','','','','A','2023-02-22','2024-02-22',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('D5568-V9700-L4557',0,'submaster',84,15,10003,0.0,'라임 크림치즈케이크','향긋함이 살아있는 라임을 치즈 속에 녹이고 바삭바삭한 쿠키시트로 식감을 더했어요','Y','Y','N','N','N','<p><img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"우리아이 간식 마카롱\\\"></p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코 무스무스 생딸기 케이크\\\">&nbsp;</p>','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"우리아이 간식 마카롱\\\"><br style=\\\"clear:both;\\\">','2023-03-15 14:52:08','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','오픈기념 쿠폰|per|0|1.0|1000',19,'nooption','','','','N',0,0,0,0,'수수료',10.00,0,'',0,'','','none','N','N','2023-03-16 15:46:47','','','Y','Y',100,18.5,'Y',33,'N','N','normal','normal','normal','N','','','','A','2022-12-30','2023-12-30',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('U4670-E5063-E9563',0,'submaster',999,2,10003,0.0,'오리지널 프레즐','굵은 소금을 뿌려 고소하고 담백한 맛이 특징인 프레즐','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"눈처럼 하얀 스노우 마카롱\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"눈처럼 하얀 스노우 마카롱\"><br style=\"clear:both;\">','2023-03-15 14:54:26','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',18,'nooption','','','','N',0,0,0,0,'수수료',10.00,0,'',0,'','','category','N','N','2023-03-29 17:41:53','','','Y','Y',100,17.5,'Y',34,'N','N','normal','normal','normal','N','','','','T','2023-03-30','2024-03-30',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,3,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('D0738-M0176-Y5381',0,'submaster',97,2,10002,0.0,'무농약 우리밀 수제 쿠키3종세트','3가지 맛을 한번에 맛볼 수 있는 기회','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"몸에 좋은 그린 마케롱 옵션\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"몸에 좋은 그린 마케롱 옵션\\\"><br style=\\\"clear:both;\\\">','2023-03-15 14:55:19','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',13,'3depth','','','','Y',3000,0,0,0,'수수료',10.00,0,'',0,'','','category','N','N','2023-03-29 17:42:05','마카롱','','Y','Y',100,12.0,'Y',23,'N','N','normal','normal','normal','N','세트','사이즈','이름','T','2023-03-30','2024-03-30',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('Q9820-Q1955-T2594',0,'submaster',999,4,10034,0.0,'생크림에 빠진 아몬드 컵케이크 - 옵션 1차','달콤한 생크림과 고소한 아몬드를 한번에 !','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코에 빠진 딸기 케이크 사이즈 옵션\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코에 빠진 딸기 케이크 사이즈 옵션\\\"><br style=\\\"clear:both;\\\">','2023-03-15 15:14:13','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',15,'1depth','','','','N',0,0,0,0,'수수료',10.00,0,'',0,'','','category','N','N','2023-03-29 17:42:23','','','Y','Y',100,14.0,'Y',22,'N','N','size','normal','normal','N','','','','A','2023-03-09','2024-03-09',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('S9950-E6332-A4864',0,'submaster',0,3,10029,0.0,'베리베리 몽땅 요거트','스트로베리, 블루베리, 라즈베리와 요거트의 환상의 조화!','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"스트로베리 몽땅 케이크\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"스트로베리 몽땅 케이크\"><br style=\"clear:both;\">','2023-03-15 15:15:28','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',17,'nooption','','','','N',0,0,0,0,'수수료',10.00,0,'',31,'','','category','N','N','2023-03-30 09:19:56','','','Y','Y',100,16.5,'Y',32,'N','N','normal','normal','normal','Y','','','','A','2023-03-09','2024-03-09',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('S5518-V0905-E2496',144,'submaster',997,3,10052,0.0,'마스카포네 티라미수 케이크 - 옵션 1차','부드러운 마스카포네 치즈와 진한 콜드브루를 담은 풍미!','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"베리베리 블루베리 케이크 컬러 이미지 옵션\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"베리베리 블루베리 케이크 컬러 이미지 옵션\\\"><br style=\\\"clear:both;\\\">','2023-03-15 15:17:05','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',14,'1depth','','','','F',0,0,0,0,'수수료',10.00,0,'3',55,'','','category','N','N','2023-03-29 17:42:40','패밀리케이크','','Y','Y',100,13.5,'Y',22,'N','N','color','normal','normal','N','','','','A','2023-03-03','2024-03-03',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-03','00:00:00','2024-03-03','00:00:00',NULL),('B9998-B2904-D7348',447,'submaster',90,9,20,0.0,'버터 크림이 들어있는 샌드 쿠키','귀여운 로봇이 새겨져 아이들도 좋아하는 맛있는 쿠키!','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"THE 스윗 로맨틱 케이크\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"THE 스윗 로맨틱 케이크\\\"><br style=\\\"clear:both;\\\">','2023-03-15 15:18:36','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','썸머할인||0|2.0|1000',33,'nooption','','','','N',0,0,0,0,'수수료',0.00,0,'',0,'','','category','N','N','2023-03-29 17:42:49','','','Y','N',100,32.5,'Y',27,'Y','N','normal','normal','normal','N','','','','A','2023-02-20','2024-02-20',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('B2776-A7254-M2918',511,'submaster',85,14,10010,0.0,'라즈베리 초코 브라우니 12개입 - 옵션 1차','당 충전이 필요할때, 하나씩 쏙쏙 꺼내 먹는 브라우니!','Y','Y','N','N','N','<br style=\\\"clear:both;\\\"><img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코 퐁당 미니츄러스 (일반1)\\\"><br style=\\\"clear:both;\\\"><br style=\\\"clear:both;\\\">','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코 퐁당 미니츄러스\\\"><br style=\\\"clear:both;\\\">','2023-03-15 15:19:44','4289636114.jpg','','1594402760.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',11,'1depth','','','','N',0,0,0,0,'수수료',10.00,0,'4,3',0,'','B2776-A7254-M2918|B9998-B2904-D7348|S5518-V0905-E2496|S9950-E6332-A4864|Q9820-Q1955-T2594|D0738-M0176-Y5381|U4670-E5063-E9563|D5568-V9700-L4557|C6364-U7256-J4618|U8905-L6749-Y3508|W0678-D3642-R9462|H7822-Q6889-R6001|A3069-L9262-I4831|A1404-E8574-G1535|A2668-K4581-X3347|A2939-W4908-C9360|A3360-Z7477-M6326|A0851-Y4325-F5506|A1561-R7770-B0752|A1853-T6120-V7609|A2476-K3606-A0582|A0344-N7288-E3075|A0369-A2723-E5596|A1293-C2219-A8817','manual','N','N','2023-03-29 17:43:00','미니츄러스','','Y','Y',100,10.5,'Y',30,'N','N','normal','normal','normal','N','타입','','','A','2023-01-10','2024-01-10',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'Y','','','','','',NULL,'N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('P9352-A0491-I1021',447,'submaster',9993,6,10042,0.0,'크루통 감자스프','부드럽고 깔끔한 풍미를 살렸습니다','Y','Y','N','N','N','상세정보','','2023-03-21 19:22:09','4289636114.jpg','','672110430.jpg','','','','','4289636114.jpg','4289636114.jpg','||0|0|0',9,'nooption','','','','',0,0,0,0,'수수료',10.00,0,'',65000,'','','none','N','N','2023-03-30 11:13:24','','','N','Y',100,8.5,'Y',24,'N','N','normal','normal','normal','N','인원선택','','','T','2023-03-30','2024-03-30',0,0,23,59,'day',10,'2022-12-30','ticket','N','day',0,4,'2022-12-22','2022-12-22','09:00','23:30','7','2022-12-23',0,'Y','','','','','','','N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('X8000-P0254-T3646',519,'submaster',9985,14,10042,2.0,'오리지널 도넛세트','','Y','Y','N','N','N','<p><img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\"></p><p><br></p>','<p>1</p>','2023-03-23 11:33:37','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','||0|0.0|0',8,'nooption','','','','',0,0,0,0,'수수료',10.00,0,'',65000,'','','none','N','N','2023-03-16 17:05:15','','','N','Y',100,7.0,'Y',30,'N','N','normal','normal','normal','N','놀이기구 선택','인원선택','','T','2023-03-30','2024-03-30',0,0,23,59,'date',3,'2022-12-31','ticket','Y','day',0,100,'2022-12-22','2022-12-31','','','1,7','2022-12-30,2023-01-26,2023-02-22',3,'Y','광주 서구 금호운천길 80-3 2층 상상너머','126.856341161825','35.1420680891065','','1661-0378','상상너머점','N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('R9603-R4372-K5481',519,'submaster',981,18,10038,0.0,'알록달록 도넛 친구세트 - 달력 예시','','Y','Y','N','N','N','<p>예매 할인 진행하고 있습니다.</p>','','2023-02-07 13:06:45','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','||0|0|0',35,'1depth','','','','',0,0,0,0,'수수료',10.00,0,'',65000,'','','category','N','N','2023-03-29 17:43:56','도넛,빵','Y','N','Y',100,34.5,'Y',30,'N','N','normal','normal','normal','N','회차 선택','','','T','2023-03-30','2024-03-30',0,0,23,59,'',30,'2023-01-27','ticket','Y','date',0,0,'2023-02-08','2023-02-08','09:00','23:30','1,2,3','2023-02-08,2023-03-16,2023-04-12,2023-04-08',1,'Y','대구 북구 엑스코로 10 (산격동, 대구전시컨벤션센터)','128.613073860533','35.9070695926156','Y','1588-1234','오디토리움','N','2023-03-04','00:00:00','2024-03-04','00:00:00',NULL),('X5785-V0661-C5698',519,'submaster',956,7,10012,0.0,'4가지맛 도넛세트 - 달력 예시','','Y','Y','N','N','N','<p>전시기간 및 안내</p>','','2023-01-25 15:39:17','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','||0|0|0',6,'1depth','','','','',0,0,0,0,'공급가',0.00,5,'3',6,'','','none','N','N','2023-03-29 17:44:04','','','N','Y',100,5.5,'Y',27,'N','N','normal','normal','normal','N','사전예매','','','T','2023-03-30','2024-03-30',0,0,23,59,'date',0,'2023-03-31','ticket','Y','date',0,0,'2023-01-25','2023-01-25','09:00','23:30','1,3','',0,'Y','광주 북구 비엔날레로 115 (용봉동, 비엔날레전시관)','126.890218160681','35.1825260286807','Y','','광주비엔날레 전시관','N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('S3629-N2275-E9290',519,'submaster',983,16,10038,0.0,'아보카도 감자샐러드를 곁들인 크레페 샌드위치 - 옵션 1차','담백하고 촉촉한 감자샐러드와 아보카도를 한번에 즐겨보세요','Y','Y','N','N','N','<p>예매 할인 진행하고 있습니다.</p>','','2023-01-25 15:29:02','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','||0|0|0',5,'1depth','','','','',0,0,0,0,'수수료',10.00,0,'',65000,'','','category','N','N','2023-03-29 17:44:13','','Y','N','Y',100,4.0,'Y',28,'N','N','normal','normal','normal','N','회차 선택','','','T','2023-03-30','2024-03-30',0,0,23,59,'day',30,'2023-01-27','ticket','N','day',0,0,'2023-03-29','2024-03-29','09:00','23:30','','',1,'Y','대구 북구 엑스코로 10 (산격동, 대구전시컨벤션센터)','128.613073860533','35.9070695926156','','1588-1234','오디토리움','N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('X5758-P1862-F7949',519,'submaster',1000,10,10011,0.1,'크림요거트 마카롱 - 달력 예시','새콤 달콤 고소한 크림요거트 마카롱','N','Y','N','N','N','<p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p><p><img src=\\\"http://responsive-food.onedaynet.co.kr/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"상큼한 과일 타르트 순우유 생크림과 치즈 무스의 환상의 조합 롤링 옵션\\\">&nbsp;</p>','','2023-03-25 11:48:21','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','오픈 할인 쿠폰|price|1000|0|0',7,'2depth','','','','',0,0,0,0,'공급가',0.00,0,'4,3',13000,'','','none','N','N','2023-03-30 10:39:17','마카롱,디저트','Y','N','Y',100,6.5,'Y',26,'Y','N','normal','normal','normal','N','세트','맛','','A','2023-01-25','2024-01-25',0,0,23,59,'',0,'2023-02-01','ticket','Y','date',0,0,'2023-01-25','2023-05-31','09:00','12:00','2','2023-01-28,2023-02-07,2023-02-14',2,'N','광주 북구 북문대로 60 (운암동, 광주문화예술회관)','126.881992972331','35.1782094522002','','062-123-4567','문화예술회관','Y','2023-03-30','09:34:00','2023-03-31','09:34:00',NULL),('N7005-W5489-B0129',147,'submaster',981,1,10038,0.0,'다크초코칩 콕콕 컵케이크 - 달력 예시','다크초코가 많이 달지않아 더욱 맛있는 컵케이크','Y','Y','N','N','N','<p>.</p>','','2023-02-10 18:36:36','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','|||0|',4,'1depth',NULL,'','','',0,0,0,0,'수수료',10.00,0,'',165000,'','','category','Y','Y','2023-03-29 18:00:39','','','N','Y',100,3.0,'Y',28,'N','N','normal','normal','normal','N','회차 선택','','','T','2023-03-30','2024-03-30',0,0,23,59,'',30,'2023-01-27','ticket','Y','day',0,0,'2023-02-08','2023-02-08','09:00','23:30','1,7','2023-02-08,2023-04-12,2023-04-08',1,'Y','대구 북구 엑스코로 10 (산격동, 대구전시컨벤션센터)','128.61307386053300661','35.907069592615599163','Y','1588-1234','오디토리움','N','2023-03-09','00:00:00','2024-03-09','00:00:00',NULL),('U8131-A5406-B1395',203,'submaster',63,36,10010,0.0,'고소 담백한 브라운 스콘 - 옵션 1차, 추가 옵션','스콘안에 견과류가 콕콕!','Y','Y','N','N','N','<img src=\\\"/upfiles/smarteditor/202212161503471433267848.jpg\\\" title=\\\"\\\" alt=\\\"초코 퐁당 미니츄러스 (일반1)\\\"><br style=\\\"clear:both;\\\"><br style=\\\"clear:both;\\\">','<img src=\"/upfiles/smarteditor/202212161503471433267848.jpg\" title=\"\" alt=\"초코 퐁당 미니츄러스\"><br style=\"clear:both;\">','2023-02-23 16:58:53','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','||0|0|0',12,'1depth','','','','N',0,0,0,0,'수수료',10.00,0,'4,3',0,'','B2776-A7254-M2918|B9998-B2904-D7348|S5518-V0905-E2496|S9950-E6332-A4864|Q9820-Q1955-T2594|D0738-M0176-Y5381|U4670-E5063-E9563|D5568-V9700-L4557|C6364-U7256-J4618|U8905-L6749-Y3508|W0678-D3642-R9462|H7822-Q6889-R6001|A3069-L9262-I4831|A1404-E8574-G1535|A2668-K4581-X3347|A2939-W4908-C9360|A3360-Z7477-M6326|A0851-Y4325-F5506|A1561-R7770-B0752|A1853-T6120-V7609|A2476-K3606-A0582|A0344-N7288-E3075|A0369-A2723-E5596|A1293-C2219-A8817','manual','N','N','2023-09-13 15:38:23','미니츄러스','','Y','Y',100,11.5,'Y',33,'Y','N','normal','normal','normal','N','타입','','','A','2023-01-10','2024-01-10',0,0,23,59,'',0,'0000-00-00','delivery','N','day',0,0,'0000-00-00','0000-00-00','','','','',0,'Y','','','','','','','N','2023-03-07','00:00:00','2024-03-07','00:00:00',NULL),('B5485-Q8863-C2763',2,'submaster',97,3,10010,0.5,'부드러운 생크림 롤케이크','입안에서 살살 녹는 부드러운 롤케이크!','N','Y','N','N','N','상세 설명','','2023-02-24 10:08:53','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','|||0|',3,'nooption','','','','N',0,0,0,0,'공급가',0.00,0,'3',15000,'','','none','N','N','2023-03-29 19:00:08','','','N','Y',100,2.5,'Y',24,'N','N','normal','normal','normal','N','','','','A','2023-02-24','2024-02-24',0,0,23,59,'',0,NULL,'delivery','N','day',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,'N','','','','',NULL,NULL,'Y','2023-03-30','09:34:08','2023-03-31','09:34:08',NULL),('Z7913-S9943-R3804',2,'submaster',98,2,10010,0.5,'라즈베리 마카롱','남녀노소 누구나 좋아하며 강력 추천하는 대표적인 마카롱!','N','Y','N','N','N','상세 설명','','2023-02-24 14:47:22','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','|||0|',1,'nooption','','','','N',0,0,0,0,'공급가',0.00,0,'3',15000,'','','none','N','N','2023-12-05 10:12:46','','','N','Y',1,1.5,'Y',28,'N','N','normal','normal','normal','N','','','','A','2023-02-24','2024-02-24',0,0,23,59,'',0,'0000-00-00','delivery','N','day',0,0,'0000-00-00','0000-00-00','','','','',0,'N','','','','','','','Y','2023-03-30','09:34:00','2023-03-31','09:34:00',NULL),('M5600-U3831-O7938',147,'submaster',94,6,10015,0.0,'화이트 초콜릿을 입은 파티케이크','특별한날에 더욱 특별한 케이크로  즐겨보세요','N','Y','N','N','N','<p>상품상세</p>','','2023-02-27 14:31:24','2292815748.jpg','','672110430.jpg','','','','','2292815748.jpg','2292815748.jpg','|||0|',2,'nooption','','','','',0,0,0,0,'수수료',10.00,0,'4,3',0,'','','none','N','N','2023-03-29 17:43:42','','','N','Y',100,1.5,'Y',22,'N','N','normal','normal','normal','N','','','','A','2023-02-27','2024-02-27',0,0,23,59,'',0,'2023-03-06','ticket','N','day',0,0,'2023-03-29','2024-03-29','09:00','23:30','','',0,'Y','','','','','','','Y','2023-03-30','09:34:08','2023-03-31','09:34:08',NULL);
/*!40000 ALTER TABLE `smart_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_addoption`
--

DROP TABLE IF EXISTS `smart_product_addoption`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_addoption` (
  `pao_uid` int(11) NOT NULL AUTO_INCREMENT,
  `pao_poptionname` varchar(200) NOT NULL COMMENT '추가옵션명',
  `pao_pcode` varchar(20) NOT NULL COMMENT '추가옵션상품코드',
  `pao_parent` varchar(200) NOT NULL COMMENT '추가옵션-부모고유번호',
  `pao_depth` varchar(5) NOT NULL COMMENT '추가옵션-depth',
  `pao_poptionprice` int(10) NOT NULL DEFAULT 0 COMMENT '옵션가격(추가)',
  `pao_poptionpurprice` int(10) NOT NULL DEFAULT 0 COMMENT '옵션공급가격(추가)',
  `pao_cnt` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '옵션 수량 - 재고',
  `pao_salecnt` int(10) NOT NULL DEFAULT 0 COMMENT '판매갯수',
  `pao_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '추가옵션보이기',
  `pao_sort` int(11) NOT NULL DEFAULT 0 COMMENT '추가옵션 순서',
  `pao_temp_cosuid` int(11) DEFAULT NULL COMMENT '공통옵션 적용 시 기존고유번호 저장(완료후 자동삭제)',
  PRIMARY KEY (`pao_uid`),
  KEY `pao_pcode` (`pao_pcode`),
  KEY `pao_depth` (`pao_depth`),
  KEY `pao_parent` (`pao_parent`)
) ENGINE=MyISAM AUTO_INCREMENT=349 DEFAULT CHARSET=utf8 COMMENT='추가옵션';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_addoption`
--

LOCK TABLES `smart_product_addoption` WRITE;
/*!40000 ALTER TABLE `smart_product_addoption` DISABLE KEYS */;
INSERT INTO `smart_product_addoption` VALUES (291,'파티용 음료 세트','A2668-K4581-X3347','','1',0,0,0,0,'Y',2,NULL),(292,'아이들이 좋아하는 캐릭터 음료','A2668-K4581-X3347','291','2',5000,0,95,5,'Y',1,NULL),(293,'부모님이 좋아하는 분위기갑 샴페인','A2668-K4581-X3347','291','2',150000,0,99,1,'Y',2,NULL),(303,'배우들과 사진촬영','X5758-P1862-F7949','302','2',9900,10000,98,1,'Y',1,NULL),(302,'사진촬영','X5758-P1862-F7949','','1',0,0,0,0,'Y',1,NULL),(301,'달달한 달고나 토핑','A1853-T6120-V7609','300','2',1000,1500,996,3,'Y',1,NULL),(300,'토핑','A1853-T6120-V7609','','1',0,0,0,0,'Y',1,NULL),(308,'선물포장','U8131-A5406-B1395','','1',0,0,0,0,'Y',0,0),(309,'선물포장 포함','U8131-A5406-B1395','308','2',100,440,98,2,'Y',0,0);
/*!40000 ALTER TABLE `smart_product_addoption` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_category`
--

DROP TABLE IF EXISTS `smart_product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_category` (
  `pct_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `pct_pcode` varchar(20) NOT NULL COMMENT '적용할 상품코드',
  `pct_cuid` int(10) NOT NULL COMMENT '적용할 최종(3차) 카테고리 고유번호',
  PRIMARY KEY (`pct_uid`),
  UNIQUE KEY `pct_unique` (`pct_pcode`,`pct_cuid`),
  KEY `pct_pcode` (`pct_pcode`),
  KEY `pct_cuid` (`pct_cuid`)
) ENGINE=MyISAM AUTO_INCREMENT=4354 DEFAULT CHARSET=utf8 COMMENT='상품 적용 카테고리 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_category`
--

LOCK TABLES `smart_product_category` WRITE;
/*!40000 ALTER TABLE `smart_product_category` DISABLE KEYS */;
INSERT INTO `smart_product_category` VALUES (3735,'H7822-Q6889-R6001',217),(3716,'A0851-Y4325-F5506',217),(3731,'A1404-E8574-G1535',144),(3714,'A3069-L9262-I4831',144),(3713,'A2668-K4581-X3347',144),(3730,'A2939-W4908-C9360',144),(3750,'U8905-L6749-Y3508',147),(3727,'A3360-Z7477-M6326',435),(3734,'A2476-K3606-A0582',203),(3749,'W0678-D3642-R9462',446),(3717,'A1561-R7770-B0752',435),(3725,'A1853-T6120-V7609',206),(3721,'A1293-C2219-A8817',435),(3724,'A0344-N7288-E3075',144),(3742,'A1561-R7770-B0752',206),(3719,'A1293-C2219-A8817',2),(3748,'W0678-D3642-R9462',447),(3747,'W0678-D3642-R9462',147),(3746,'W0678-D3642-R9462',449),(3745,'W0678-D3642-R9462',450),(3743,'A0369-A2723-E5596',217),(3744,'A1853-T6120-V7609',449),(3756,'V8942-E6872-C1909',2),(3752,'U8905-L6749-Y3508',447),(3753,'U8905-L6749-Y3508',449),(3754,'U8905-L6749-Y3508',450),(3755,'U8905-L6749-Y3508',435),(3757,'V8942-E6872-C1909',203),(3758,'V8942-E6872-C1909',487),(3759,'V8942-E6872-C1909',449),(3760,'C6364-U7256-J4618',450),(3761,'C6364-U7256-J4618',490),(3762,'C6364-U7256-J4618',206),(3763,'C6364-U7256-J4618',2),(3764,'C6364-U7256-J4618',487),(3765,'D5568-V9700-L4557',490),(3766,'D5568-V9700-L4557',144),(3767,'D5568-V9700-L4557',450),(3768,'D5568-V9700-L4557',488),(3769,'D5568-V9700-L4557',2),(3770,'D5568-V9700-L4557',468),(3771,'U8717-A1792-Q7353',217),(3772,'U8717-A1792-Q7353',464),(3773,'U8717-A1792-Q7353',491),(3774,'U8717-A1792-Q7353',447),(3775,'U8717-A1792-Q7353',478),(3776,'U4670-E5063-E9563',203),(3777,'U4670-E5063-E9563',490),(3778,'U4670-E5063-E9563',492),(3779,'U4670-E5063-E9563',493),(3780,'U4670-E5063-E9563',144),(3781,'D0738-M0176-Y5381',451),(3782,'D0738-M0176-Y5381',450),(3783,'D0738-M0176-Y5381',487),(3784,'D0738-M0176-Y5381',2),(3785,'D0738-M0176-Y5381',435),(3786,'D0738-M0176-Y5381',447),(3787,'Q9820-Q1955-T2594',450),(3788,'Q9820-Q1955-T2594',487),(3789,'Q9820-Q1955-T2594',451),(3790,'Q9820-Q1955-T2594',147),(3791,'Q9820-Q1955-T2594',2),(3792,'S9950-E6332-A4864',446),(3793,'S9950-E6332-A4864',488),(3794,'S9950-E6332-A4864',489),(3795,'S9950-E6332-A4864',449),(3796,'S9950-E6332-A4864',468),(3801,'S5518-V0905-E2496',452),(3802,'S5518-V0905-E2496',450),(3803,'S5518-V0905-E2496',147),(3804,'S5518-V0905-E2496',2),(3805,'B9998-B2904-D7348',450),(3806,'B9998-B2904-D7348',144),(3807,'B9998-B2904-D7348',147),(3808,'B9998-B2904-D7348',2),(3809,'B9998-B2904-D7348',435),(3810,'B2776-A7254-M2918',446),(3811,'B2776-A7254-M2918',2),(3812,'B2776-A7254-M2918',447),(3813,'B2776-A7254-M2918',206),(3814,'A3069-L9262-I4831',2),(3815,'A1404-E8574-G1535',2),(3816,'A2668-K4581-X3347',1),(3817,'A3360-Z7477-M6326',217),(3818,'A3360-Z7477-M6326',451),(3819,'A1561-R7770-B0752',2),(3820,'A2476-K3606-A0582',478),(3821,'A2476-K3606-A0582',493),(3822,'A0344-N7288-E3075',206),(3823,'A0369-A2723-E5596',203),(3824,'A1293-C2219-A8817',493),(3825,'A1293-C2219-A8817',144),(3827,'A3069-L9262-I4831',500),(3828,'U8905-L6749-Y3508',494),(3829,'B2776-A7254-M2918',511),(3834,'X8000-P0254-T3646',2),(3833,'P9352-A0491-I1021',2),(3897,'B9998-B2904-D7348',447),(3895,'X5785-V0661-C5698',144),(3896,'X5785-V0661-C5698',1),(3891,'X5758-P1862-F7949',144),(3892,'X5758-P1862-F7949',1),(3893,'S3629-N2275-E9290',144),(3894,'S3629-N2275-E9290',147),(3898,'R9603-R4372-K5481',144),(3899,'R9603-R4372-K5481',147),(3900,'G0628-Z0070-J0256',446),(3901,'Y7007-V3424-U8505',446),(3902,'V5062-Y5897-P8449',446),(3903,'Y7120-J3227-T0304',446),(3904,'S3249-H4042-Y2313',446),(3905,'H1758-H0799-Z0939',446),(3906,'F0273-K4823-O6999',446),(3907,'O9693-C0272-H7751',446),(3911,'L0408-P1246-B3595',144),(3912,'D0036-Z0412-N8673',144),(3913,'B8126-L2462-U7362',144),(3922,'N7005-W5489-B0129',144),(3923,'A2668-K4581-X3347',206),(3924,'X8000-P0254-T3646',206),(3933,'R9603-R4372-K5481',217),(3932,'R9603-R4372-K5481',203),(3931,'R9603-R4372-K5481',206),(3934,'R9603-R4372-K5481',464),(3935,'R9603-R4372-K5481',490),(3936,'R9603-R4372-K5481',491),(3937,'X8000-P0254-T3646',491),(3938,'X8000-P0254-T3646',464),(3939,'X5758-P1862-F7949',464),(3940,'X5758-P1862-F7949',203),(3941,'U8131-A5406-B1395',2),(3942,'U8131-A5406-B1395',206),(3943,'U8131-A5406-B1395',446),(3944,'U8131-A5406-B1395',447),(3945,'U8131-A5406-B1395',511),(3946,'B5485-Q8863-C2763',206),(3947,'M4117-Z7877-O5728',203),(3948,'Z7913-S9943-R3804',206),(3953,'Z7913-S9943-R3804',489),(3954,'Z7913-S9943-R3804',2),(3955,'B5485-Q8863-C2763',203),(3956,'B5485-Q8863-C2763',2),(3957,'U8131-A5406-B1395',203),(3958,'S5518-V0905-E2496',144),(3959,'W0678-D3642-R9462',1),(3960,'A0344-N7288-E3075',2),(3961,'A0344-N7288-E3075',147),(3962,'A1853-T6120-V7609',435),(3963,'A1853-T6120-V7609',447),(3964,'A1404-E8574-G1535',217),(3965,'A1404-E8574-G1535',447),(3966,'A2668-K4581-X3347',500),(3967,'A2939-W4908-C9360',147),(3968,'A2939-W4908-C9360',1),(3969,'A0851-Y4325-F5506',2),(3970,'A0851-Y4325-F5506',447),(3971,'P9352-A0491-I1021',519),(3972,'P9352-A0491-I1021',1),(3973,'P9352-A0491-I1021',447),(3974,'X8000-P0254-T3646',519),(3975,'X5758-P1862-F7949',519),(3976,'S3629-N2275-E9290',519),(3977,'X5785-V0661-C5698',519),(3978,'R9603-R4372-K5481',519),(3979,'N7005-W5489-B0129',519),(3980,'N7005-W5489-B0129',147),(3981,'M5600-U3831-O7938',519),(3982,'M5600-U3831-O7938',144),(3983,'M5600-U3831-O7938',147),(4316,'J2896-P3691-O1255',450),(4320,'I0102-C1217-N6391',449),(3989,'Z7913-S9943-R3804',491),(3994,'M5600-U3831-O7938',206),(3995,'M5600-U3831-O7938',491),(4001,'B5485-Q8863-C2763',491),(4006,'N7005-W5489-B0129',206),(4007,'N7005-W5489-B0129',491),(4013,'S3629-N2275-E9290',206),(4014,'S3629-N2275-E9290',491),(4029,'X5785-V0661-C5698',206),(4030,'X5785-V0661-C5698',491),(4036,'X5758-P1862-F7949',206),(4037,'X5758-P1862-F7949',491),(4050,'P9352-A0491-I1021',206),(4051,'P9352-A0491-I1021',491),(4058,'A2668-K4581-X3347',491),(4064,'B2776-A7254-M2918',491),(4071,'U8131-A5406-B1395',491),(4079,'D0738-M0176-Y5381',206),(4080,'D0738-M0176-Y5381',491),(4089,'S5518-V0905-E2496',206),(4090,'S5518-V0905-E2496',491),(4098,'Q9820-Q1955-T2594',206),(4099,'Q9820-Q1955-T2594',491),(4107,'H7822-Q6889-R6001',206),(4108,'H7822-Q6889-R6001',491),(4112,'S9950-E6332-A4864',206),(4113,'S9950-E6332-A4864',491),(4121,'U4670-E5063-E9563',206),(4122,'U4670-E5063-E9563',491),(4130,'D5568-V9700-L4557',206),(4131,'D5568-V9700-L4557',491),(4140,'C6364-U7256-J4618',491),(4147,'U8905-L6749-Y3508',206),(4148,'U8905-L6749-Y3508',491),(4157,'W0678-D3642-R9462',206),(4158,'W0678-D3642-R9462',491),(4167,'A3069-L9262-I4831',206),(4168,'A3069-L9262-I4831',491),(4174,'A2939-W4908-C9360',206),(4175,'A2939-W4908-C9360',491),(4181,'A3360-Z7477-M6326',206),(4182,'A3360-Z7477-M6326',491),(4188,'A0851-Y4325-F5506',206),(4189,'A0851-Y4325-F5506',491),(4195,'A1561-R7770-B0752',491),(4200,'A1853-T6120-V7609',491),(4206,'A2476-K3606-A0582',206),(4207,'A2476-K3606-A0582',491),(4213,'A0344-N7288-E3075',491),(4219,'A0369-A2723-E5596',206),(4220,'A0369-A2723-E5596',491),(4225,'A1293-C2219-A8817',206),(4226,'A1293-C2219-A8817',491),(4233,'B9998-B2904-D7348',206),(4234,'B9998-B2904-D7348',491),(4243,'A1404-E8574-G1535',206),(4244,'A1404-E8574-G1535',491),(4321,'G4906-A0968-Z8332',144),(4322,'G4906-A0968-Z8332',519),(4323,'L5435-T0306-X1600',144),(4325,'E5583-Y0328-O2228',144);
/*!40000 ALTER TABLE `smart_product_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_category_best`
--

DROP TABLE IF EXISTS `smart_product_category_best`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_category_best` (
  `pctb_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `pctb_pcode` varchar(20) NOT NULL COMMENT '상품',
  `pctb_cuid` int(11) NOT NULL COMMENT '카테고리',
  `pctb_idx` int(8) unsigned NOT NULL COMMENT '베스트상품등록순위',
  `pctb_sort_group` int(8) NOT NULL DEFAULT 100 COMMENT '베스트상품 우선순위 그룹',
  `pctb_sort_idx` decimal(8,1) NOT NULL COMMENT '베스트상품우선순위그룹내 순위',
  PRIMARY KEY (`pctb_uid`),
  UNIQUE KEY `pctb_pcode` (`pctb_pcode`,`pctb_cuid`)
) ENGINE=MyISAM AUTO_INCREMENT=133 DEFAULT CHARSET=utf8 COMMENT='베스트카테고리 상품';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_category_best`
--

LOCK TABLES `smart_product_category_best` WRITE;
/*!40000 ALTER TABLE `smart_product_category_best` DISABLE KEYS */;
INSERT INTO `smart_product_category_best` VALUES (21,'A2476-K3606-A0582',447,1,100,0.5),(2,'Q9820-Q1955-T2594',144,9,100,8.5),(17,'A2668-K4581-X3347',144,3,100,2.5),(20,'A1293-C2219-A8817',447,2,100,1.5),(5,'B9998-B2904-D7348',144,10,100,9.5),(90,'Z7913-S9943-R3804',147,12,100,11.5),(89,'M5600-U3831-O7938',147,13,100,12.5),(29,'U4670-E5063-E9563',144,2,100,2.0),(28,'D5568-V9700-L4557',144,4,100,3.5),(11,'W0678-D3642-R9462',1,1,100,0.5),(12,'U8905-L6749-Y3508',1,2,100,1.5),(13,'D5568-V9700-L4557',1,3,100,2.5),(14,'D0738-M0176-Y5381',1,4,100,3.5),(15,'B2776-A7254-M2918',1,5,100,4.5),(16,'B2776-A7254-M2918',2,1,100,0.5),(22,'U4670-E5063-E9563',447,3,100,2.5),(23,'D0738-M0176-Y5381',447,4,100,3.5),(32,'S3629-N2275-E9290',144,1,100,0.5),(25,'S9950-E6332-A4864',144,7,100,6.5),(26,'S5518-V0905-E2496',144,8,100,7.5),(30,'D0738-M0176-Y5381',144,5,100,4.5),(31,'B2776-A7254-M2918',144,6,100,5.5),(88,'B5485-Q8863-C2763',147,14,100,13.5),(87,'N7005-W5489-B0129',147,15,100,14.5),(86,'S3629-N2275-E9290',147,16,100,15.5),(85,'R9603-R4372-K5481',147,17,100,16.5),(84,'X5758-P1862-F7949',147,18,100,17.5),(83,'X8000-P0254-T3646',147,19,100,18.5),(39,'S9950-E6332-A4864',449,1,100,0.5),(40,'B2776-A7254-M2918',449,2,100,1.5),(82,'A2668-K4581-X3347',147,20,100,19.5),(81,'B2776-A7254-M2918',147,21,100,20.5),(80,'U8131-A5406-B1395',147,22,100,21.5),(79,'S5518-V0905-E2496',147,23,100,22.5),(78,'Q9820-Q1955-T2594',147,24,100,23.5),(77,'H7822-Q6889-R6001',147,11,100,10.5),(76,'U4670-E5063-E9563',147,10,100,9.5),(72,'W0678-D3642-R9462',147,1,100,0.5),(70,'A3360-Z7477-M6326',147,2,100,1.5),(69,'A0851-Y4325-F5506',147,3,100,2.5),(68,'A1561-R7770-B0752',147,4,100,3.5),(67,'A1853-T6120-V7609',147,5,100,4.5),(66,'A2476-K3606-A0582',147,6,100,5.5),(65,'A0344-N7288-E3075',147,7,100,6.5),(64,'A0369-A2723-E5596',147,8,100,7.5),(63,'B9998-B2904-D7348',147,9,100,8.5),(62,'A1404-E8574-G1535',147,25,100,24.5),(128,'R9603-R4372-K5481',491,2,100,1.5),(127,'X8000-P0254-T3646',491,1,100,1.0);
/*!40000 ALTER TABLE `smart_product_category_best` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_guide`
--

DROP TABLE IF EXISTS `smart_product_guide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_guide` (
  `g_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `g_user` varchar(50) NOT NULL COMMENT '적용입점업체아이디',
  `g_type` tinyint(4) NOT NULL COMMENT '안내구분',
  `g_default` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '기본적용여부',
  `g_title` varchar(100) NOT NULL COMMENT '타이틀',
  `g_content` text NOT NULL COMMENT '이용안내내용',
  `g_mdate` datetime NOT NULL COMMENT '최종수정일',
  `g_rdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`g_uid`),
  KEY `g_user` (`g_user`,`g_type`,`g_default`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='상품 이용안내 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_guide`
--

LOCK TABLES `smart_product_guide` WRITE;
/*!40000 ALTER TABLE `smart_product_guide` DISABLE KEYS */;
INSERT INTO `smart_product_guide` VALUES (7,'gobeyond',20,'Y','불가한 상품안내','<p>ㅇ 주문취소</p><p>&nbsp; - 결제대기, 결제완료 상태인 경우에는 가능(신용카드, 휴대폰 결제 시), 무통장입금 시에는 +1일 소요됨</p><p>&nbsp; - 상품준비중, 배송중 상태인 경우 불가</p><p>&nbsp;</p><p><br></p><p>ㅇ 환불/반품/반송</p><p>&nbsp; - 결제대기, 결제완료 상태인 경우에는 당일 환불가능(신용카드, 휴대폰 결제 시), 무통장입금 시에는 +1일 소요됨</p><p>&nbsp; - 상품준비중, 배송중 상태인 경우 고객센터 또는 1:1문의를 통해 신청 가능, 상품이 배송중일 시 [반송비 5,000원(도서산간 10,000원)/송장 개수 기준] 무통장으로 선입금 후 환불 진행</p><p>&nbsp; - 배송완료, 구매확정 상태인 경우 수령일로부터 7일 이내 고객센터 또는 1:1문의를 통해 신청 가능, [반송비 5,000원(도서산간 10,000원)/송장 개수 기준] 무통장으로 선입금 후 환불 진행</p><p><br></p><p><br></p><p>&nbsp; ★ 환불 및 반품 절차</p><p>&nbsp; &nbsp; &nbsp;① 고객센터 또는 1:1문의를 통해 신청</p><p>&nbsp; &nbsp; &nbsp;② 반송비 5,000원을 꽁쇼핑의 무통장입금 진행(도서산간 10,000원)/송장 개수 기준</p><p>&nbsp; &nbsp; &nbsp;③ 상품이 아직 배송중인 경우 수취거부를, 이미 수령하신 경우 박스에 재포장 후 대기</p><p>&nbsp; &nbsp; &nbsp;④ 입점사에서 보낸 반송택배기사를 통해 상품 반송 진행</p><p>&nbsp; &nbsp; &nbsp;⑤ 반송된 상품이 입점사의 물류창고로 수령 및 상품 상태확인</p><p>&nbsp; &nbsp; &nbsp;⑥ 상품에 이상이 없을 경우 환불 진행</p><p><br></p><p><br></p><p>&nbsp; ★ 환불 기준</p><p>&nbsp; &nbsp; &nbsp;① 밀봉스티커 등의 밀봉처리가 된 상품은 미개봉 상태여야 함</p><p>&nbsp; &nbsp; &nbsp;② 상품 택이 있는 제품은 택을 제거하지 않아야 함</p><p>&nbsp; &nbsp; &nbsp;③ 상품 사이즈 비교 등을 위해 착장 시 화장품, 이물질 등이 묻어 변질 시 불가</p><p>&nbsp; &nbsp; &nbsp;④ 수령 시 배송 중 상품에 하자가 있는 경우 환불 가능 무통장으로 선입금 후 환불 진행</p><p><br></p><p><br></p><p>ㅇ 교환</p><p>&nbsp; - 배송완료, 구매확정 상태인 경우 수령일로부터 7일 이내 고객센터 또는 1:1문의를 통해 신청 가능, [반송비 5,000원(도서산간 10,000원)/송장 개수 기준] 무통장으로 선입금 후 교환 진행</p><p><br></p><p>&nbsp; ★ 교환 및 반품 절차&nbsp;</p><p>&nbsp; &nbsp; &nbsp;① 고객센터 또는 1:1문의를 통해 신청</p><p>&nbsp; &nbsp; &nbsp;② 반송비 5,000원을 꽁쇼핑의 무통장입금 진행(도서산간 10,000원)/송장 개수 기준</p><p>&nbsp; &nbsp; &nbsp;③ 상품을 박스에 재포장 후 대기</p><p>&nbsp; &nbsp; &nbsp;④ 입점사에서 보낸 반송택배기사를 통해 상품 반송 진행</p><p>&nbsp; &nbsp; &nbsp;⑤ 반송된 상품이 입점사의 물류창고로 수령 및 상품 상태확인</p><p>&nbsp; &nbsp; &nbsp;⑥ 상품에 이상이 없을 경우 교환 진행<br></p><p><br></p><p>&nbsp; ★ 교환 기준</p><p>&nbsp; &nbsp; &nbsp;① 밀봉스티커 등의 밀봉처리가 된 상품은 미개봉 상태여야 함</p><p>&nbsp; &nbsp; &nbsp;② 상품 택이 있는 제품은 택을 제거하지 않아야 함</p><p>&nbsp; &nbsp; &nbsp;③ 상품 사이즈 비교 등을 위해 착장 시 화장품, 이물질 등이 묻어 변질 시 불가</p><p>&nbsp; &nbsp; &nbsp;④ 수령 시 배송 중 상품에 하자가 있는 경우 맞교환 가능</p><p><br></p><p><br></p><p>ㅇ 소비자분쟁해결 기준(공정거래위원회 고시)에 따라 피해를 보상받을 수 있습니다.</p><p>ㅇ A/S는 상세페이지 상단에 표기된 \\\'상품문의 연락처\\\'로 문의하시기 바랍니다.</p>','2023-03-21 17:36:50','2023-03-03 16:27:30'),(25,'_MASTER_',10,'Y','구매/배송안내222','<p>구매/배송안내<br></p>','2023-03-21 17:22:25','2023-03-21 16:38:17'),(8,'gobeyond',10,'N','배송 구매/배송안내 - 기본','<p>ㅇ 배송비 : 기본배송료는 2,500원이며 상품에 따라 완전 또는 조건부 무료배송일 수 있습니다. (도서,산간,오지 일부지역은 배송비가 추가될 수 있습니다)</p><p><br></p><p>ㅇ 본 상품의 평균 배송일은 결제완료 후 평일기준 3일입니다.</p><p>&nbsp; - 배송지연 시 SMS로 안내 드립니다.</p><p>&nbsp; - 발송 후 수령하시는 지역 택배영업소의 상황에 따라 다소 차이가 있을 수 있습니다.</p><p>&nbsp; - 주말 및 공휴일은 발송하지 않습니다.</p>','2023-03-21 17:15:50','2023-03-02 16:27:58');
/*!40000 ALTER TABLE `smart_product_guide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_icon`
--

DROP TABLE IF EXISTS `smart_product_icon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_icon` (
  `pi_uid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `pi_type` varchar(50) NOT NULL COMMENT '아이콘 유형 var.php  에서 지정',
  `pi_img` varchar(30) DEFAULT NULL COMMENT '[이미지형] 아이콘 이미지 반응형에서 미사용',
  `pi_img_m` varchar(30) NOT NULL COMMENT '[이미지형] 상품아이콘 - 모바일전용',
  `pi_title` varchar(200) DEFAULT NULL COMMENT '아이콘 타이틀',
  `pi_idx` int(5) NOT NULL DEFAULT 0 COMMENT '노출 순서',
  `pi_rdate` datetime DEFAULT NULL COMMENT '아이콘등록일',
  `pi_view_type` enum('img','text') DEFAULT 'img' COMMENT '아이콘 등록 형태 (img: 이미지형, text:텍스트형)',
  `pi_text_color` varchar(7) DEFAULT NULL COMMENT '[텍스트형] 아이콘 글씨색상',
  `pi_bg_color` varchar(7) DEFAULT NULL COMMENT '[텍스트형] 아이콘 배경색상',
  `pi_line_color` varchar(7) DEFAULT NULL COMMENT '[텍스트형] 아이콘 라인색상',
  PRIMARY KEY (`pi_uid`),
  KEY `pi_type` (`pi_type`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='상품아이콘관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_icon`
--

LOCK TABLES `smart_product_icon` WRITE;
/*!40000 ALTER TABLE `smart_product_icon` DISABLE KEYS */;
INSERT INTO `smart_product_icon` VALUES (2,'product_coupon_small_icon','','','COUPON',1,'2023-03-29 00:00:00','text','#e10000','#ffffff','#e10000'),(1,'product_freedelivery_small_icon','','','무료배송',1,'2023-03-29 00:00:00','text','#ffffff','#000000','#000000'),(3,'product_name_small_icon','','','NEW',2,'2023-03-29 13:52:17','text','#000000','#ffffff','#000000'),(4,'product_name_small_icon','','','HOT',1,'2023-03-29 13:52:28','text','#ffffff','#000000','#000000'),(6,'product_promotion_small_icon','','','EVENT',1,'2023-03-29 10:07:31','text','#ffffff','#e10000','#e10000'),(13,'product_ticket_small_icon','','','TICKET',1,'2023-01-03 09:11:50','text','#000000','#ffffff','#000000');
/*!40000 ALTER TABLE `smart_product_icon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_latest`
--

DROP TABLE IF EXISTS `smart_product_latest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_latest` (
  `pl_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pl_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `pl_uniqkey` varchar(30) NOT NULL DEFAULT '' COMMENT 'viewing 회원 고유키 - 쿠키에 의한 지속',
  `pl_rdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`pl_uid`),
  KEY `pl_pcode` (`pl_pcode`),
  KEY `pl_uniqkey` (`pl_uniqkey`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='최근본상품관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_latest`
--

LOCK TABLES `smart_product_latest` WRITE;
/*!40000 ALTER TABLE `smart_product_latest` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_product_latest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_option`
--

DROP TABLE IF EXISTS `smart_product_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_option` (
  `po_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `po_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `po_poptionname` varchar(100) DEFAULT NULL COMMENT '옵션명',
  `po_cnt` int(10) NOT NULL DEFAULT 0 COMMENT '옵션 수량',
  `po_salecnt` int(10) NOT NULL DEFAULT 0 COMMENT '옵션 판매량',
  `po_poptionprice` int(10) DEFAULT 0 COMMENT '옵션가격',
  `po_poption_supplyprice` int(10) NOT NULL DEFAULT 0 COMMENT '옵션 공급가',
  `po_depth` tinyint(2) unsigned DEFAULT 1 COMMENT '옵션 차수 - 1,2,3차',
  `po_parent` varchar(200) DEFAULT NULL COMMENT '부모 uid 지정',
  `po_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '옵션보이기',
  `po_sort` int(11) NOT NULL COMMENT '옵션순서',
  `po_temp_cosuid` int(11) DEFAULT NULL COMMENT '공통옵션 적용 시 기존고유번호 저장(완료후 자동삭제)',
  `po_color_type` enum('color','img') DEFAULT NULL COMMENT 'color타입 - color:색상, img:이미지',
  `po_color_name` varchar(50) DEFAULT NULL COMMENT 'po_color_type에 따른 지정값 - color일 경우 colorpicker , img일 경우 파일명',
  PRIMARY KEY (`po_uid`),
  KEY `po_pcode` (`po_pcode`),
  KEY `po_depth` (`po_depth`),
  KEY `po_parent` (`po_parent`),
  KEY `po_poptionname` (`po_poptionname`),
  KEY `po_view` (`po_view`),
  KEY `po_color_type` (`po_color_type`)
) ENGINE=MyISAM AUTO_INCREMENT=6741 DEFAULT CHARSET=utf8 COMMENT='상품옵션관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_option`
--

LOCK TABLES `smart_product_option` WRITE;
/*!40000 ALTER TABLE `smart_product_option` DISABLE KEYS */;
INSERT INTO `smart_product_option` VALUES (6163,'A2476-K3606-A0582','1개',100,0,15000,0,2,'6162','Y',1,NULL,'',''),(6164,'A2476-K3606-A0582','올댓재즈',0,0,0,0,1,NULL,'Y',2,NULL,'',''),(6165,'A2476-K3606-A0582','1개',1000,0,1500,0,2,'6164','Y',1,NULL,'',''),(6166,'A2476-K3606-A0582','썸띵',0,0,0,0,1,NULL,'Y',3,NULL,'',''),(6133,'A0369-A2723-E5596','화이트',96,4,1000,1000,1,NULL,'Y',2,NULL,'color','#000000'),(6115,'A1404-E8574-G1535','헤어 미스트',0,0,0,0,1,NULL,'Y',1,NULL,'',''),(6132,'A0369-A2723-E5596','네이비',98,2,1000,1000,1,NULL,'Y',0,NULL,'img','3644471933.gif'),(6131,'A0344-N7288-E3075','화이트',99,0,12000,13000,1,NULL,'Y',2,NULL,'color','#f2f2f2'),(6130,'A0344-N7288-E3075','네이비',99,0,10000,15000,1,NULL,'Y',1,NULL,'color','#0f243e'),(6162,'A2476-K3606-A0582','더스티핑크',0,0,0,0,1,NULL,'Y',1,NULL,'',''),(6149,'A1853-T6120-V7609','3장',0,0,0,0,2,'6146','Y',3,NULL,'',''),(6148,'A1853-T6120-V7609','2장',0,0,0,0,2,'6146','Y',2,NULL,'',''),(6152,'A1853-T6120-V7609','치즈 추가',0,0,0,0,1,NULL,'Y',8,NULL,'',''),(6147,'A1853-T6120-V7609','1장',0,3,0,0,2,'6146','Y',1,NULL,'',''),(6146,'A1853-T6120-V7609','햄 추가',0,0,0,0,1,NULL,'Y',7,NULL,'',''),(6145,'A1293-C2219-A8817','2세트',999,0,39000,40000,1,NULL,'Y',2,NULL,'',''),(6144,'A1293-C2219-A8817','1세트',999,0,21000,22000,1,NULL,'Y',1,NULL,'',''),(6153,'A1853-T6120-V7609','1장',0,0,0,0,2,'6152','Y',1,NULL,'',''),(6154,'A1853-T6120-V7609','샐러드 추가',0,0,0,0,1,NULL,'Y',9,NULL,'',''),(6155,'A1853-T6120-V7609','양상추',0,0,0,0,2,'6154','Y',1,NULL,'',''),(6157,'A1853-T6120-V7609','3장',0,0,0,0,2,'6152','Y',3,NULL,'',''),(6158,'A1853-T6120-V7609','2장',0,1,0,0,2,'6152','Y',2,NULL,'',''),(6634,'A1853-T6120-V7609','',0,0,0,0,3,'6146,6147','Y',1,NULL,'',''),(6167,'A2476-K3606-A0582','1개',1000,0,1500,0,2,'6166','Y',1,NULL,'',''),(6168,'A2476-K3606-A0582','20개',1000,0,25000,0,2,'6162','Y',4,NULL,'',''),(6169,'A2476-K3606-A0582','10개',1000,0,13000,0,2,'6162','Y',3,NULL,'',''),(6170,'A2476-K3606-A0582','5개',100,0,12000,0,2,'6162','Y',2,NULL,'',''),(6171,'A2476-K3606-A0582','20개',1000,0,25000,0,2,'6164','Y',4,NULL,'',''),(6172,'A2476-K3606-A0582','10개',1000,0,13000,0,2,'6164','Y',3,NULL,'',''),(6173,'A2476-K3606-A0582','5개',1000,0,7000,0,2,'6164','Y',2,NULL,'',''),(6174,'A2476-K3606-A0582','20개',1000,0,25000,0,2,'6166','Y',4,NULL,'',''),(6175,'A2476-K3606-A0582','10개',1000,0,13000,0,2,'6166','Y',3,NULL,'',''),(6176,'A2476-K3606-A0582','5개',1000,0,7000,0,2,'6166','Y',2,NULL,'',''),(6177,'A2668-K4581-X3347','기본 토핑',-1,3,0,0,1,NULL,'Y',1,NULL,'',''),(6178,'A2668-K4581-X3347','베이컨+토마토+치즈',0,3,0,0,1,NULL,'Y',2,NULL,'',''),(6179,'A1404-E8574-G1535','화이트',0,0,0,0,2,'6115','Y',1,NULL,'color','#ffffff'),(6180,'A1404-E8574-G1535','50ml',100,0,5000,8000,3,'6115,6179','Y',1,NULL,'','#ffffff'),(6181,'A1404-E8574-G1535','오렌지',0,0,0,0,2,'6115','Y',2,NULL,'color','#e36c09'),(6182,'A1404-E8574-G1535','50ml',5000,0,5000,8000,3,'6115,6181','Y',1,NULL,'','#e36c09'),(6183,'A1404-E8574-G1535','블루',0,0,0,0,2,'6115','Y',3,NULL,'color','#366092'),(6184,'A1404-E8574-G1535','100ml',999,0,5000,8000,3,'6115,6183','Y',1,NULL,'','#366092'),(6185,'A1404-E8574-G1535','100ml',100,0,5000,8000,3,'6115,6179','Y',2,NULL,'','#ffffff'),(6186,'A1404-E8574-G1535','100ml',5000,0,5000,8000,3,'6115,6181','Y',2,NULL,'','#e36c09'),(6187,'A1404-E8574-G1535','100ml',999,0,5000,8000,3,'6115,6183','Y',2,NULL,'','#366092'),(6189,'A2668-K4581-X3347','패밀리 1호',0,2,0,0,2,'6177','Y',1,NULL,'',''),(6190,'A2668-K4581-X3347','양상추 포함',0,2,0,0,2,'6178','Y',1,NULL,'',''),(6191,'A2668-K4581-X3347','커플 2호',0,2,0,0,2,'6178','Y',2,NULL,'',''),(6194,'A2668-K4581-X3347','라지',999,0,1000,1000,3,'6177,6189','Y',1,NULL,'',''),(6198,'Q9820-Q1955-T2594','패밀리 사이즈(가장 잘 팔리는 상품입니다. 사이즈 뿐만 아니라 맛도 2배로 업 시킨 케이크!)',999,2,328000,0,1,NULL,'Y',1,NULL,'',''),(6195,'A2668-K4581-X3347','미디엄',991,9,1000,1000,3,'6178,6190','Y',1,NULL,'',''),(6196,'A2668-K4581-X3347','스몰',975,25,1000,1000,3,'6178,6191','Y',1,NULL,'',''),(6199,'Q9820-Q1955-T2594','스몰 사이즈',985,1,29000,0,1,NULL,'Y',3,NULL,'',''),(6200,'B2776-A7254-M2918','4개입',9992,8,10000,1000,1,NULL,'Y',1,NULL,'img','63677131.jpeg'),(6201,'B2776-A7254-M2918','',0,0,0,0,2,'6200','Y',1,NULL,NULL,NULL),(6232,'Q9820-Q1955-T2594','1+1 할인행사',999,0,328000,0,2,'6198','Y',1,NULL,'',''),(6202,'S5518-V0905-E2496','맛있는 블루베리 생크림 케이크',97,2,34000,35000,1,NULL,'Y',1,NULL,'img','1913725920.jpg'),(6203,'S5518-V0905-E2496','딸기 무스 케이크',99,0,36000,38000,1,NULL,'Y',2,NULL,'img','1064824977.jpg'),(6204,'H7822-Q6889-R6001','레드 크림',0,1,0,0,1,NULL,'Y',1,NULL,'color','#ff0000'),(6205,'H7822-Q6889-R6001','화이트 생크림',0,0,0,0,1,NULL,'Y',2,NULL,'color','#f2f2f2'),(6206,'H7822-Q6889-R6001','퍼플 크림',0,0,0,0,1,NULL,'Y',3,NULL,'color','#7030a0'),(6207,'Q9820-Q1955-T2594','파티 사이즈',0,0,29000,0,1,NULL,'Y',4,NULL,'',''),(6208,'S5518-V0905-E2496','쿠키 케이크',0,0,33000,35000,1,NULL,'Y',3,NULL,'img','1673829289.jpg'),(6209,'D0738-M0176-Y5381','도넛 세트',0,0,0,0,1,NULL,'Y',1,NULL,'',''),(6210,'D0738-M0176-Y5381','달달한 도넛',0,0,0,0,2,'6209','Y',1,NULL,'',''),(6211,'D0738-M0176-Y5381','초록마을 도넛',997,2,4500,5000,3,'6209,6210','Y',1,NULL,'',''),(6212,'D0738-M0176-Y5381','도넛 단품',0,0,0,0,1,NULL,'Y',3,NULL,'',''),(6213,'D0738-M0176-Y5381','빅사이즈 도넛',0,0,0,0,2,'6212','Y',1,NULL,'',''),(6214,'D0738-M0176-Y5381','무지개 도넛',999,0,5000,5500,3,'6212,6213','Y',1,NULL,'',''),(6215,'A1293-C2219-A8817','레드',100,0,5000,0,2,'6144','Y',1,NULL,'color','#ff0000'),(6216,'A1293-C2219-A8817','블랙',100,0,5000,0,2,'6145','Y',1,NULL,'color','#000000'),(6217,'A1293-C2219-A8817','mini',100,0,5000,0,3,'6144,6215','Y',1,NULL,'','#ff0000'),(6218,'A1293-C2219-A8817','mini',100,0,5000,0,3,'6145,6216','Y',1,NULL,'','#000000'),(6220,'B2776-A7254-M2918','8개입',997,3,1000,1000,1,NULL,'Y',2,NULL,'color','#000000'),(6221,'B2776-A7254-M2918','36개입',0,0,10000,1000,1,NULL,'Y',4,NULL,'color','#000000'),(6222,'B2776-A7254-M2918','16개입',0,0,0,0,1,NULL,'Y',3,NULL,'color','#000000'),(6223,'B2776-A7254-M2918','2세트',998,2,10000,0,1,NULL,'Y',6,NULL,'color','#000000'),(6224,'B2776-A7254-M2918','1세트',1000,0,1000,0,1,NULL,'Y',5,NULL,'color','#000000'),(6225,'D0738-M0176-Y5381','보라마을 도넛',0,0,4500,5000,3,'6209,6210','Y',3,NULL,'',''),(6226,'H7822-Q6889-R6001','라지',999,0,12000,15000,2,'6204','Y',1,NULL,'','#ff0000'),(6227,'H7822-Q6889-R6001','미디엄',999,0,12000,13000,2,'6205','Y',1,NULL,'','#f2f2f2'),(6228,'H7822-Q6889-R6001','스몰',999,0,14000,15000,2,'6206','Y',1,NULL,'','#7030a0'),(6229,'H7822-Q6889-R6001','SSS',100,0,1000,1000,3,'6204,6226','Y',1,NULL,'','#ff0000'),(6230,'H7822-Q6889-R6001','SS',1000,0,1000,10000,3,'6205,6227','Y',1,NULL,'','#f2f2f2'),(6231,'H7822-Q6889-R6001','LARGE',100,0,1000,1000,3,'6206,6228','Y',1,NULL,'','#7030a0'),(6233,'Q9820-Q1955-T2594','1+1 할인행사',985,0,29000,0,2,'6199','Y',1,NULL,'',''),(6234,'Q9820-Q1955-T2594','1+1 할인행사',0,0,0,0,2,'6207','Y',1,NULL,'',''),(6378,'X8000-P0254-T3646','아동',9999,0,1000,0,2,'6252','Y',3,NULL,'',''),(6373,'X8000-P0254-T3646','청소년 (키 150cm이상)',998,1,3000,0,2,'6254','Y',2,NULL,'',''),(6374,'X8000-P0254-T3646','성인',9996,3,10000,0,2,'6253','Y',1,NULL,'',''),(6375,'X8000-P0254-T3646','유아',9999,0,2000,0,2,'6253','Y',2,NULL,'',''),(6376,'X8000-P0254-T3646','성인',9998,1,1000,0,2,'6252','Y',1,NULL,'',''),(6377,'X8000-P0254-T3646','유아',9997,2,1000,0,2,'6252','Y',2,NULL,'',''),(6372,'X8000-P0254-T3646','성인',992,7,10000,0,2,'6254','Y',1,NULL,'',''),(6254,'X8000-P0254-T3646','박치기 자동차',0,0,0,0,1,'','Y',1,0,'',''),(6253,'X8000-P0254-T3646','회전목마',0,0,0,0,1,'','Y',2,0,'',''),(6252,'X8000-P0254-T3646','동물원 이용권',0,0,0,0,1,'','Y',3,0,'',''),(6251,'P9352-A0491-I1021','아동',100,0,5000,0,1,NULL,'Y',3,NULL,'',''),(6250,'P9352-A0491-I1021','유아',10,0,25000,0,1,NULL,'Y',2,NULL,'',''),(6249,'P9352-A0491-I1021','성인',10,0,45000,0,1,NULL,'Y',1,NULL,'',''),(6394,'S3629-N2275-E9290','2회차 14시 00분',90,9,35000,90000,1,NULL,'Y',2,NULL,'',''),(6393,'S3629-N2275-E9290','1회차 11시 00분',92,7,35000,90000,1,NULL,'Y',1,NULL,'',''),(6384,'X5758-P1862-F7949','1세트',0,0,0,0,1,NULL,'Y',1,NULL,'',''),(6385,'X5758-P1862-F7949','고소한 맛',93,6,110000,130000,2,'6384','Y',1,NULL,'',''),(6391,'X5758-P1862-F7949','달달한 맛',97,2,88000,99000,2,'6389','Y',2,NULL,'',''),(6389,'X5758-P1862-F7949','2세트',0,0,0,0,1,NULL,'Y',2,NULL,'',''),(6395,'X5785-V0661-C5698','1세트',996,5,12221,12000,1,NULL,'Y',1,NULL,'',''),(6396,'X5785-V0661-C5698','2세트',998,2,5050,6000,1,NULL,'Y',2,NULL,'',''),(6397,'X5785-V0661-C5698','3세트',999,0,3030,4000,1,NULL,'Y',3,NULL,'',''),(6398,'R9603-R4372-K5481','2회차 14시 00분',90,0,35000,90000,1,'','Y',2,0,'',''),(6399,'R9603-R4372-K5481','1회차 11시 00분',90,2,35000,90000,1,'','Y',1,0,'',''),(6439,'U8131-A5406-B1395','12개입',0,0,10000,1000,1,'','Y',4,0,'',''),(6436,'U8131-A5406-B1395','2개입',9989,2,10000,1000,1,'','Y',1,0,'',''),(6437,'U8131-A5406-B1395','',0,0,0,0,2,'6436','Y',1,0,'',''),(6438,'U8131-A5406-B1395','4개입',981,16,1000,1000,1,'','Y',2,0,'',''),(6435,'N7005-W5489-B0129','2회차 14시 00분',90,0,35000,90000,1,NULL,'Y',2,NULL,NULL,NULL),(6434,'N7005-W5489-B0129','1회차 11시 00분',90,1,35000,90000,1,NULL,'Y',1,NULL,NULL,NULL),(6440,'U8131-A5406-B1395','8개입',0,0,0,0,1,'','Y',3,0,'',''),(6441,'U8131-A5406-B1395','48개입',998,0,10000,0,1,'','Y',6,0,'',''),(6442,'U8131-A5406-B1395','24개입',997,3,1000,0,1,'','Y',5,0,'',''),(6574,'B2776-A7254-M2918','',0,0,0,0,1,NULL,'Y',7,NULL,'color','#000000'),(6575,'B2776-A7254-M2918','',0,0,0,0,1,NULL,'Y',8,NULL,'color','#000000'),(6576,'B2776-A7254-M2918','',0,0,0,0,1,NULL,'Y',9,NULL,'color','#000000'),(6577,'B2776-A7254-M2918','',0,0,0,0,1,NULL,'Y',10,NULL,'color','#000000'),(6578,'A0369-A2723-E5596','',0,0,0,0,1,NULL,'Y',3,NULL,'color','#000000'),(6580,'D0738-M0176-Y5381','',0,0,0,0,2,'6212','Y',3,NULL,'',''),(6581,'D0738-M0176-Y5381','',0,0,0,0,3,'6209,6210','Y',4,NULL,'',''),(6582,'D0738-M0176-Y5381','',0,0,0,0,3,'6209,6210','Y',5,NULL,'',''),(6583,'D0738-M0176-Y5381','',0,0,0,0,1,NULL,'Y',2,NULL,'',''),(6584,'D0738-M0176-Y5381','',0,0,0,0,2,'6212','Y',2,NULL,'',''),(6585,'D0738-M0176-Y5381','',0,0,0,0,2,'6212','Y',4,NULL,'',''),(6586,'D0738-M0176-Y5381','',0,0,0,0,2,'6212','Y',5,NULL,'',''),(6587,'D0738-M0176-Y5381','',0,0,0,0,1,NULL,'Y',4,NULL,'',''),(6588,'D0738-M0176-Y5381','',0,0,0,0,2,'6587','Y',1,NULL,'',''),(6589,'D0738-M0176-Y5381','',0,0,0,0,2,'6212','Y',6,NULL,'',''),(6590,'D0738-M0176-Y5381','',0,0,0,0,1,NULL,'Y',5,NULL,'',''),(6591,'D0738-M0176-Y5381','',0,0,0,0,1,NULL,'Y',6,NULL,'',''),(6592,'D0738-M0176-Y5381','',0,0,0,0,2,'6591','Y',1,NULL,'',''),(6593,'D0738-M0176-Y5381','',0,0,0,0,1,NULL,'Y',7,NULL,'',''),(6594,'D0738-M0176-Y5381','',0,0,0,0,2,'6593','Y',1,NULL,'',''),(6595,'D0738-M0176-Y5381','',0,0,0,0,1,NULL,'Y',8,NULL,'',''),(6617,'D0738-M0176-Y5381','',0,0,0,0,2,'6209','Y',2,NULL,'',''),(6597,'D0738-M0176-Y5381','',0,0,0,0,1,NULL,'Y',9,NULL,'',''),(6598,'D0738-M0176-Y5381','',0,0,0,0,2,'6597','Y',1,NULL,'',''),(6599,'D0738-M0176-Y5381','',0,0,0,0,3,'6597,6598','Y',1,NULL,'',''),(6604,'D0738-M0176-Y5381','',0,0,0,0,2,'6209','Y',4,NULL,'',''),(6618,'D0738-M0176-Y5381','',0,0,0,0,2,'6587','Y',2,NULL,'',''),(6619,'D0738-M0176-Y5381','',0,0,0,0,2,'6591','Y',2,NULL,'',''),(6614,'D0738-M0176-Y5381','',0,0,0,0,2,'6209','Y',3,NULL,'',''),(6622,'D0738-M0176-Y5381','',0,0,0,0,1,NULL,'Y',10,NULL,'',''),(6623,'D0738-M0176-Y5381','',0,0,0,0,2,'6622','Y',1,NULL,'',''),(6624,'D0738-M0176-Y5381','',0,0,0,0,2,'6583','Y',1,NULL,'',''),(6625,'D0738-M0176-Y5381','',0,0,0,0,2,'6583','Y',2,NULL,'',''),(6626,'D0738-M0176-Y5381','',0,0,0,0,2,'6209','Y',5,NULL,'',''),(6628,'D0738-M0176-Y5381','',0,0,0,0,3,'6583,6625','Y',1,NULL,'',''),(6629,'D0738-M0176-Y5381','',0,0,0,0,3,'6209,6617','Y',1,NULL,'',''),(6630,'D0738-M0176-Y5381','',0,0,0,0,3,'6209,6210','Y',2,NULL,'',''),(6635,'A1853-T6120-V7609','',0,0,0,0,3,'6146,6147','Y',2,NULL,'',''),(6636,'A1853-T6120-V7609','',0,0,0,0,3,'6146,6147','Y',3,NULL,'',''),(6637,'A1853-T6120-V7609','',0,0,0,0,2,'6146','Y',4,NULL,NULL,NULL),(6638,'A1853-T6120-V7609','',0,0,0,0,3,'6146,6637','Y',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `smart_product_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_option_tmp`
--

DROP TABLE IF EXISTS `smart_product_option_tmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_option_tmp` (
  `pot_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `pot_pucuid` int(11) NOT NULL COMMENT '옵션 업로드 고유번호',
  `pot_pcode` varchar(20) NOT NULL COMMENT '적용할 상품코드',
  `pot_info` text NOT NULL COMMENT '엑셀로 업로드한 상품 전체 옵션 줄임정보 - §(옵션구분), >(차수구분), |(항목구분)',
  `pot_rdate` datetime NOT NULL COMMENT '등록일시',
  PRIMARY KEY (`pot_uid`),
  KEY `pot_pcode` (`pot_pcode`),
  KEY `pot_pucuid` (`pot_pucuid`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='엑셀 업로드 상품옵션 임시관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_option_tmp`
--

LOCK TABLES `smart_product_option_tmp` WRITE;
/*!40000 ALTER TABLE `smart_product_option_tmp` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_product_option_tmp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_req_info`
--

DROP TABLE IF EXISTS `smart_product_req_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_req_info` (
  `pri_uid` int(11) NOT NULL AUTO_INCREMENT,
  `pri_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `pri_key` varchar(200) NOT NULL COMMENT '항목명',
  `pri_value` text NOT NULL COMMENT '항목값',
  `pri_rdate` datetime NOT NULL COMMENT '저장일',
  PRIMARY KEY (`pri_uid`),
  KEY `pri_pcode` (`pri_pcode`)
) ENGINE=MyISAM AUTO_INCREMENT=588 DEFAULT CHARSET=utf8 COMMENT='정보제공고시';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_req_info`
--

LOCK TABLES `smart_product_req_info` WRITE;
/*!40000 ALTER TABLE `smart_product_req_info` DISABLE KEYS */;
INSERT INTO `smart_product_req_info` VALUES (307,'A2668-K4581-X3347','제품소개','암꽃게(국내산) 40% 간장[양조간장, 산분해간장, 탈지대두(외국산:미국,인도,중국)] 35% 물엿(국내산) 11%, [다시마(국내산), 양파(국내산), 대파(국내산), 무(국내산)] 9% 청양고추(국내산) 2%, 소주 1.5%,  마늘(국내산) 1.2%, 생강(국내산) 0.3%','0000-00-00 00:00:00'),(308,'A2668-K4581-X3347','색상','양념꽃게장(비살균제품)','0000-00-00 00:00:00'),(309,'A2668-K4581-X3347','사이즈','사이즈','0000-00-00 00:00:00'),(310,'A2668-K4581-X3347','제조사','사이즈','0000-00-00 00:00:00'),(311,'A2668-K4581-X3347','A/S 책임자와 전화번호','사이즈','0000-00-00 00:00:00'),(312,'A2668-K4581-X3347','제조국','사이즈','0000-00-00 00:00:00'),(313,'A2668-K4581-X3347','취급시주의사항','','0000-00-00 00:00:00'),(314,'A2668-K4581-X3347','품질보증기준','암꽃게(국내산) 40% 간장[양조간장, 산분해간장, 탈지대두(외국산:미국,인도,중국)] 35% 물엿(국내산) 11%, [다시마(국내산), 양파(국내산), 대파(국내산), 무(국내산)] 9% 청양고추(국내산) 2%, 소주 1.5%,  마늘(국내산) 1.2%, 생강(국내산) 0.3%','0000-00-00 00:00:00'),(475,'_DEFAULT_SETTING_','품질보증기준','','0000-00-00 00:00:00'),(473,'_DEFAULT_SETTING_','제조국','','0000-00-00 00:00:00'),(474,'_DEFAULT_SETTING_','취급시주의사항','','0000-00-00 00:00:00'),(471,'_DEFAULT_SETTING_','제조사','','0000-00-00 00:00:00'),(472,'_DEFAULT_SETTING_','A/S 책임자와 전화번호','','0000-00-00 00:00:00'),(470,'_DEFAULT_SETTING_','사이즈','','0000-00-00 00:00:00'),(469,'_DEFAULT_SETTING_','색상','','0000-00-00 00:00:00'),(468,'_DEFAULT_SETTING_','제품소개','','0000-00-00 00:00:00'),(508,'_DEFAULT_TICKET_SETT','티켓 사용처','상세정보 참고','0000-00-00 00:00:00'),(509,'_DEFAULT_TICKET_SETT','취소/환불','상세정보 참고','0000-00-00 00:00:00'),(510,'_DEFAULT_TICKET_SETT','기타','상세정보 참고','0000-00-00 00:00:00'),(528,'_DEFAULT_T_SETTING_','기타2','상세입니다.\r\n반가워요','0000-00-00 00:00:00'),(525,'_DEFAULT_T_SETTING_','티켓 사용처','상세\r\n참고바람1','0000-00-00 00:00:00'),(526,'_DEFAULT_T_SETTING_','취소/환불','상세참고바람2','0000-00-00 00:00:00'),(527,'_DEFAULT_T_SETTING_','기타','상세참고바람3','0000-00-00 00:00:00'),(529,'_DEFAULT_T_SETTING_','기타3','상세참고사항\r\n입니다.','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `smart_product_req_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_talk`
--

DROP TABLE IF EXISTS `smart_product_talk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_talk` (
  `pt_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pt_type` varchar(20) NOT NULL DEFAULT '' COMMENT '댓글타입:상품토크,상품후기 등',
  `pt_pcode` varchar(20) DEFAULT NULL COMMENT '상품코드',
  `pt_inid` varchar(50) NOT NULL COMMENT '등록 회원',
  `pt_writer` varchar(50) NOT NULL DEFAULT '' COMMENT '작성자 이름',
  `pt_intype` enum('normal','admin','company') NOT NULL DEFAULT 'normal' COMMENT '등록 회원 유형 normal:일반회원, admin:운영자, company:해당입점업체',
  `pt_title` varchar(255) NOT NULL COMMENT '제목',
  `pt_content` mediumtext DEFAULT NULL COMMENT '댓글내용',
  `pt_rdate` datetime DEFAULT NULL COMMENT '등록일',
  `pt_depth` tinyint(2) DEFAULT 1 COMMENT '1:부모 , 2:자식글 구분',
  `pt_relation` int(10) DEFAULT 0 COMMENT '부모글 m_uid, 자식글이 아니면 0',
  `pt_eval_point` int(11) NOT NULL DEFAULT 0 COMMENT '평점',
  `pt_img` varchar(200) NOT NULL COMMENT '상품평가 이미지',
  `pt_hit` int(11) NOT NULL DEFAULT 0 COMMENT '조회수',
  PRIMARY KEY (`pt_uid`),
  KEY `pt_type` (`pt_type`),
  KEY `pt_inid` (`pt_inid`),
  KEY `pt_pcode` (`pt_pcode`),
  KEY `pt_depth` (`pt_depth`),
  KEY `pt_relation` (`pt_relation`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COMMENT='상품 토크,후기,문의 ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_talk`
--

LOCK TABLES `smart_product_talk` WRITE;
/*!40000 ALTER TABLE `smart_product_talk` DISABLE KEYS */;
INSERT INTO `smart_product_talk` VALUES (1,'상품리뷰','A2668-K4581-X3347','master','관리자','normal','','문의도 했었는데 친절하게 응대해주시고 좋았어요~!','2023-02-15 10:00:39',1,0,100,'',6),(40,'상품리뷰','U8131-A5406-B1395','master','관리자','normal','','배송도 빠르고 상품도 맘에 듭니다~! 다음에도 또 주문할게요^^','2023-03-20 11:42:56',1,0,100,'',5),(42,'상품리뷰','C6364-U7256-J4618','master','관리자','normal','','배송이 빨랐어요','2023-03-20 11:43:49',1,0,100,'',26),(39,'상품리뷰','N7005-W5489-B0129','master','관리자','normal','','배송도 빠르고 좋아요!','2023-03-20 11:42:12',1,0,100,'',4),(41,'상품리뷰','B9998-B2904-D7348','master','관리자','normal','','좋아요~ 굳굳!','2023-03-20 11:43:25',1,0,100,'',7),(14,'상품리뷰','D5568-V9700-L4557','master','관리자','normal','','너무너무 맘에 들어요~! 추천해요','2023-03-02 11:25:15',1,0,100,'',9),(17,'상품리뷰','P3225-K4000-X8414','master','관리자','normal','','좋아요! 다음에 또 구매할게요','2023-03-03 10:36:12',1,0,100,'',2),(43,'상품리뷰','C6364-U7256-J4618','master','관리자','normal','','조금 아쉬워요','2023-03-20 11:44:10',1,0,20,'',6),(45,'상품문의','A0851-Y4325-F5506','master','관리자','normal','배송관련 문의입니다.','지금 OO물류센터에 몇일째 머물러있는거 같아요..ㅠㅠ 혹시 언제쯤 받을 수 있을까요??','2023-03-20 11:45:46',1,0,0,'',0),(44,'상품문의','N7005-W5489-B0129','master','관리자','normal','유통기한 알 수 있을까요?','유통기한은 어디서 확인할 수 있을까요? 잘 못 찾겠네요..','2023-03-20 11:45:12',1,0,0,'',0),(46,'상품문의','S5518-V0905-E2496','master','관리자','normal','안녕하세요. 단체주문 문의 드립니다.','이번에 어린이집에 보내려고 10개 정도 주문하려고하는데 배송이 한번에 진행되는지 궁금합니다. 원하는 날짜에 받을 수 있을까요? 답변 부탁드립니다.','2023-03-20 11:46:09',1,0,0,'',0),(47,'상품문의','A0851-Y4325-F5506','master','관리자','admin','','안녕하세요~\r\n\r\n우선 불편을 드려 죄송합니다. 기사님께 확인해본 결과 분실된거 같다고 하네요..\r\n\r\n환불 조치 해드리겠습니다.','2023-03-20 11:46:24',2,45,0,'',0);
/*!40000 ALTER TABLE `smart_product_talk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_talk_report`
--

DROP TABLE IF EXISTS `smart_product_talk_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_talk_report` (
  `ptr_uid` int(11) NOT NULL AUTO_INCREMENT,
  `ptr_inid` varchar(30) NOT NULL COMMENT '신고자 아이디',
  `ptr_ptuid` int(11) NOT NULL COMMENT '리뷰고유번호',
  `ptr_reason` int(11) NOT NULL COMMENT 'var.php  에  $arr_reposrt_reason 참고',
  `ptr_reason_etc` text DEFAULT NULL COMMENT '신고사유가 기타일 경우 처리',
  `ptr_status` enum('R','N','Y','C') NOT NULL DEFAULT 'N' COMMENT '(R:접수, N:미확인,Y:확인,C:반려 - 정상글일경우) ',
  `ptr_status_update` datetime NOT NULL COMMENT '상태값 업데이트일',
  `ptr_rdate` datetime NOT NULL COMMENT '신고등록일',
  PRIMARY KEY (`ptr_uid`),
  KEY `ptr_status` (`ptr_status`),
  KEY `ptr_ptuid` (`ptr_ptuid`),
  KEY `ptr_inid` (`ptr_inid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품리뷰 신고하기 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_talk_report`
--

LOCK TABLES `smart_product_talk_report` WRITE;
/*!40000 ALTER TABLE `smart_product_talk_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_product_talk_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_tmpoption`
--

DROP TABLE IF EXISTS `smart_product_tmpoption`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_tmpoption` (
  `pto_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `pto_pouid` int(10) unsigned NOT NULL COMMENT '옵션고유번호 - 3차일 경우 3차 옵션 uid',
  `pto_mid` varchar(50) NOT NULL COMMENT '회원아이디',
  `pto_pcode` varchar(20) NOT NULL COMMENT '구매상품코드',
  `pto_cnt` int(10) unsigned DEFAULT 0 COMMENT '구매상품갯수',
  `pto_poptionname1` varchar(100) DEFAULT NULL COMMENT '구매상품 1차옵션명',
  `pto_poptionname2` varchar(100) DEFAULT NULL COMMENT '구매상품 2차옵션명',
  `pto_poptionname3` varchar(100) DEFAULT NULL COMMENT '구매상품 3차옵션명',
  `pto_poption_supplyprice` int(10) NOT NULL DEFAULT 0 COMMENT '공급가',
  `pto_poptionprice` int(10) DEFAULT 0 COMMENT '구매상품옵션가격',
  `pto_is_addoption` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '추가옵션여부',
  `pto_addoption_parent` int(11) NOT NULL COMMENT '추가옵션일때 필수옵션 uid',
  `pto_old_poptionprice` int(11) NOT NULL COMMENT '기존금액저장',
  `pto_groupset_price_per` decimal(3,1) NOT NULL COMMENT '상품선택당시 할인율',
  `pto_dateoption_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '달력옵션 사용여부(Y:사용,N:미사용)',
  `pto_dateoption_date` date DEFAULT NULL COMMENT '달력옵션 사용시 선택된 날짜',
  PRIMARY KEY (`pto_uid`),
  KEY `pto_pouid` (`pto_pouid`),
  KEY `pto_mid` (`pto_mid`),
  KEY `pto_pcode` (`pto_pcode`),
  KEY `pto_dateoption_use_pto_dateoption_date` (`pto_dateoption_use`,`pto_dateoption_date`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='주문적용을 위한 임시 옵션관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_tmpoption`
--

LOCK TABLES `smart_product_tmpoption` WRITE;
/*!40000 ALTER TABLE `smart_product_tmpoption` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_product_tmpoption` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_upload_count`
--

DROP TABLE IF EXISTS `smart_product_upload_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_upload_count` (
  `puc_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `puc_cnt` int(11) NOT NULL COMMENT '업로드상품수',
  `puc_aid` varchar(50) NOT NULL COMMENT '관리자 아이디',
  `puc_rdate` datetime NOT NULL COMMENT '등록일시',
  PRIMARY KEY (`puc_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='엑셀 업로드 수량 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_upload_count`
--

LOCK TABLES `smart_product_upload_count` WRITE;
/*!40000 ALTER TABLE `smart_product_upload_count` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_product_upload_count` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_product_wish`
--

DROP TABLE IF EXISTS `smart_product_wish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_product_wish` (
  `pw_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pw_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `pw_inid` varchar(30) NOT NULL DEFAULT '' COMMENT '찜한 개인회원 아이디',
  `pw_rdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`pw_uid`),
  KEY `pw_pcode` (`pw_pcode`),
  KEY `pw_inid` (`pw_inid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 찜한상품관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_product_wish`
--

LOCK TABLES `smart_product_wish` WRITE;
/*!40000 ALTER TABLE `smart_product_wish` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_product_wish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_promotion_attend_addinfo`
--

DROP TABLE IF EXISTS `smart_promotion_attend_addinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_promotion_attend_addinfo` (
  `ata_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `ata_event` int(11) NOT NULL COMMENT '출석체크 이벤트 고유번호',
  `ata_days` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '달성조건 - 출석일수',
  `ata_coupon` int(11) NOT NULL DEFAULT 0 COMMENT '지급쿠폰 고유번호',
  `ata_point` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '지급포인트',
  `ata_coupon_delay` int(5) NOT NULL DEFAULT 0 COMMENT '쿠폰지급일',
  `ata_point_delay` int(5) NOT NULL DEFAULT 0 COMMENT '포인트지급일',
  `ata_rdate` datetime NOT NULL COMMENT '등록/수정일',
  PRIMARY KEY (`ata_uid`),
  KEY `ata_event` (`ata_event`),
  KEY `ata_days` (`ata_days`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COMMENT='출석체크 이벤트 달성조건 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_promotion_attend_addinfo`
--

LOCK TABLES `smart_promotion_attend_addinfo` WRITE;
/*!40000 ALTER TABLE `smart_promotion_attend_addinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_promotion_attend_addinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_promotion_attend_config`
--

DROP TABLE IF EXISTS `smart_promotion_attend_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_promotion_attend_config` (
  `atc_uid` int(11) NOT NULL AUTO_INCREMENT,
  `atc_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '출석이벤트 사용여부',
  `atc_title` varchar(100) NOT NULL COMMENT '이벤트 제목',
  `atc_limit` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '기간제한여부(Y-이벤트기간동안사용,N-기간제한없음)',
  `atc_sdate` date NOT NULL COMMENT '이벤트 시작일',
  `atc_edate` date NOT NULL COMMENT '이벤트 종료일',
  `atc_type` enum('T','C') NOT NULL DEFAULT 'T' COMMENT '참여방식(T-누적 참여형,C-연속 참여형)',
  `atc_duplicate` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '혜택 중복적용',
  `atc_mdate` datetime NOT NULL COMMENT '수정일',
  `atc_img_pc` varchar(50) NOT NULL COMMENT '타이틀이미지PC',
  `atc_img_mo` varchar(50) NOT NULL COMMENT '타이틀이미지Mobile',
  `atc_rdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`atc_uid`),
  KEY `atc_use` (`atc_use`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='출석이벤트 설정';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_promotion_attend_config`
--

LOCK TABLES `smart_promotion_attend_config` WRITE;
/*!40000 ALTER TABLE `smart_promotion_attend_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_promotion_attend_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_promotion_attend_coupon_ready`
--

DROP TABLE IF EXISTS `smart_promotion_attend_coupon_ready`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_promotion_attend_coupon_ready` (
  `acr_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `acr_status` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '발급상태',
  `acr_inid` varchar(30) NOT NULL COMMENT '발급회원',
  `acr_ocsuid` int(11) NOT NULL COMMENT '발급쿠폰 고유번호',
  `acr_atluid` int(11) NOT NULL COMMENT '적용한 출석체크 로그 고유번호',
  `acr_idate` date NOT NULL COMMENT '발급예정일',
  `acr_rdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`acr_uid`),
  KEY `acr_status` (`acr_status`),
  KEY `acr_atluid` (`acr_atluid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='출석체크 발급대기쿠폰관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_promotion_attend_coupon_ready`
--

LOCK TABLES `smart_promotion_attend_coupon_ready` WRITE;
/*!40000 ALTER TABLE `smart_promotion_attend_coupon_ready` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_promotion_attend_coupon_ready` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_promotion_attend_log`
--

DROP TABLE IF EXISTS `smart_promotion_attend_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_promotion_attend_log` (
  `atl_uid` int(11) NOT NULL AUTO_INCREMENT,
  `atl_event` int(11) NOT NULL COMMENT '이벤트 uid (smart_attand_config - atc_uid)',
  `atl_member` varchar(100) NOT NULL COMMENT '회원 아이디',
  `atl_date` date NOT NULL COMMENT '출석체크 날짜',
  `atl_status` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '연속출석체크',
  `atl_addinfo_uid` int(11) NOT NULL DEFAULT 0 COMMENT '지급조건 달성시 대상 달성조건 고유번호',
  `atl_addinfo_days` int(11) NOT NULL DEFAULT 0 COMMENT '달성조건 - 출석일수',
  `atl_success` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '달성조건 달성여부',
  `atl_coupon` int(11) NOT NULL DEFAULT 0 COMMENT '지급한 쿠폰 고유번호',
  `atl_coupon_name` varchar(100) NOT NULL COMMENT '지급한 쿠폰명',
  `atl_point` int(11) NOT NULL DEFAULT 0 COMMENT '지급한 포인트',
  `atl_rdate` datetime NOT NULL COMMENT '등록일시',
  `atl_addinfo_days_count` int(11) NOT NULL COMMENT '달성조건 - 출석일수중 몇번째인지',
  PRIMARY KEY (`atl_uid`),
  KEY `atl_event` (`atl_event`),
  KEY `atl_member` (`atl_member`),
  KEY `atl_date` (`atl_date`),
  KEY `atl_status` (`atl_status`),
  KEY `atl_addinfo_uid` (`atl_addinfo_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='출석체크';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_promotion_attend_log`
--

LOCK TABLES `smart_promotion_attend_log` WRITE;
/*!40000 ALTER TABLE `smart_promotion_attend_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_promotion_attend_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_promotion_code`
--

DROP TABLE IF EXISTS `smart_promotion_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_promotion_code` (
  `pr_uid` int(11) NOT NULL AUTO_INCREMENT,
  `pr_code` varchar(200) NOT NULL COMMENT '프로모션코드',
  `pr_rdate` datetime NOT NULL COMMENT '생성일',
  `pr_expire_date` date NOT NULL COMMENT '만료일',
  `pr_edate` datetime NOT NULL COMMENT '수정일',
  `pr_name` varchar(200) NOT NULL COMMENT '코드명',
  `pr_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '사용여부(Y:사용, N:미사용)',
  `pr_type` enum('P','A') NOT NULL DEFAULT 'A' COMMENT '타입(P:할인율, A:할인금액)',
  `pr_amount` int(11) NOT NULL COMMENT '금액(할인율)',
  `pr_expire` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '만료여부(Y:만료, N:사용가능)',
  `pr_min_order_price` int(11) NOT NULL DEFAULT 0 COMMENT '최소주문금액',
  `pr_price_max_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '할인율(P) 일 경우 최대로 사용 가능한 금액 설정여부(Y:사용, N:미사용)',
  `pr_price_max` int(11) NOT NULL DEFAULT 0 COMMENT '할인율(P) 일 경우 최대 할인금액 (pr_price_max_use 가 Y 일 경우에만 적용됨)',
  `pr_due_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '중복사용 설정 여부 (Y: 중복사용 가능, N:중복사용 불가능)',
  PRIMARY KEY (`pr_uid`),
  KEY `pr_code` (`pr_code`),
  KEY `pr_expire` (`pr_expire`),
  KEY `pr_type` (`pr_type`),
  KEY `pr_use` (`pr_use`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='프로모션 코드';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_promotion_code`
--

LOCK TABLES `smart_promotion_code` WRITE;
/*!40000 ALTER TABLE `smart_promotion_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_promotion_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_promotion_plan`
--

DROP TABLE IF EXISTS `smart_promotion_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_promotion_plan` (
  `pp_uid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `pp_title` varchar(200) DEFAULT NULL COMMENT '기획전명',
  `pp_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '노출여부',
  `pp_sdate` date DEFAULT NULL COMMENT '시작일',
  `pp_edate` date DEFAULT NULL COMMENT '종료일',
  `pp_rdate` datetime NOT NULL COMMENT '등록일',
  `pp_dummy` tinyint(1) DEFAULT NULL COMMENT 'smart_table_text를 통해 pp_content(PC내용), pp_content_m(모바일 내용) 등록함.',
  `pp_img` varchar(50) NOT NULL COMMENT '프로모션 목록 이미지',
  PRIMARY KEY (`pp_uid`),
  KEY `pp_view` (`pp_view`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='기획전관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_promotion_plan`
--

LOCK TABLES `smart_promotion_plan` WRITE;
/*!40000 ALTER TABLE `smart_promotion_plan` DISABLE KEYS */;
INSERT INTO `smart_promotion_plan` VALUES (13,'프로모션 기획전 쇼핑몰 오픈 이벤트를 시작합니다.','Y','2023-03-30','2024-03-30','2023-03-27 09:52:26',NULL,'4181139140.jpg'),(15,'프로모션 기획전 쇼핑 특가','Y','2023-03-30','2024-03-30','2023-03-27 10:07:07',NULL,'1143934192.jpg');
/*!40000 ALTER TABLE `smart_promotion_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_promotion_plan_product_setup`
--

DROP TABLE IF EXISTS `smart_promotion_plan_product_setup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_promotion_plan_product_setup` (
  `ppps_uid` int(10) NOT NULL AUTO_INCREMENT,
  `ppps_ppuid` int(8) NOT NULL COMMENT '기획전 고유번호 - 임시 추가일 경우 pp_uid가 아닌 add 로 입력함',
  `ppps_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `ppps_idx` int(8) unsigned NOT NULL COMMENT '기획전 상품등록순위',
  `ppps_sort_group` int(8) NOT NULL DEFAULT 100 COMMENT '기획전 우선순위 그룹',
  `ppps_sort_idx` decimal(8,1) NOT NULL COMMENT '기획전 우선순위그룹내 순위',
  PRIMARY KEY (`ppps_uid`),
  KEY `ppps_ppuid` (`ppps_ppuid`),
  KEY `ppps_pcode` (`ppps_pcode`)
) ENGINE=MyISAM AUTO_INCREMENT=347 DEFAULT CHARSET=utf8 COMMENT='기획전 상품 설정 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_promotion_plan_product_setup`
--

LOCK TABLES `smart_promotion_plan_product_setup` WRITE;
/*!40000 ALTER TABLE `smart_promotion_plan_product_setup` DISABLE KEYS */;
INSERT INTO `smart_promotion_plan_product_setup` VALUES (113,15,'A0851-Y4325-F5506',65,100,65.0),(112,15,'A3360-Z7477-M6326',61,100,60.5),(111,15,'A2939-W4908-C9360',62,100,61.5),(110,15,'A3069-L9262-I4831',64,100,63.5),(109,15,'W0678-D3642-R9462',63,100,63.0),(297,15,'N7005-W5489-B0129',36,100,35.5),(295,15,'B5485-Q8863-C2763',38,100,37.5),(102,15,'Q9820-Q1955-T2594',60,100,59.5),(101,15,'S5518-V0905-E2496',59,100,58.5),(100,15,'D0738-M0176-Y5381',53,100,52.5),(121,15,'A1404-E8574-G1535',66,100,65.5),(99,15,'B2776-A7254-M2918',54,100,53.5),(288,13,'X5785-V0661-C5698',1,100,0.5),(286,13,'S3629-N2275-E9290',3,100,2.5),(285,13,'N7005-W5489-B0129',4,100,3.5),(284,13,'B5485-Q8863-C2763',5,100,4.5),(283,13,'Z7913-S9943-R3804',6,100,5.5),(123,15,'P9352-A0491-I1021',67,100,67.0),(281,15,'M5600-U3831-O7938',52,100,51.5),(118,15,'A0369-A2723-E5596',55,100,54.5),(117,15,'A0344-N7288-E3075',56,100,55.5),(116,15,'A2476-K3606-A0582',57,100,56.5),(115,15,'A1853-T6120-V7609',58,100,57.5),(280,15,'Z7913-S9943-R3804',43,100,42.5),(299,15,'S3629-N2275-E9290',49,100,48.5),(300,15,'R9603-R4372-K5481',48,100,47.5),(303,15,'X5785-V0661-C5698',46,100,45.5),(305,15,'X5758-P1862-F7949',50,100,49.5),(306,15,'X8000-P0254-T3646',35,100,34.5),(310,15,'A2668-K4581-X3347',14,100,13.5),(313,15,'U8131-A5406-B1395',11,100,10.5),(317,15,'H7822-Q6889-R6001',7,100,6.5),(320,15,'S9950-E6332-A4864',4,100,3.5),(322,15,'U4670-E5063-E9563',2,100,1.5),(324,15,'D5568-V9700-L4557',17,100,16.5),(326,15,'C6364-U7256-J4618',32,100,31.5),(327,15,'U8905-L6749-Y3508',31,100,30.5),(335,15,'A1561-R7770-B0752',23,100,22.5);
/*!40000 ALTER TABLE `smart_promotion_plan_product_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_request`
--

DROP TABLE IF EXISTS `smart_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_request` (
  `r_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `r_inid` varchar(50) NOT NULL COMMENT '등록한 개인회원',
  `r_comname` varchar(50) NOT NULL COMMENT '이름/상호명',
  `r_tel` varchar(50) NOT NULL COMMENT '연락처',
  `r_hp` varchar(50) NOT NULL COMMENT '핸드폰',
  `r_email` varchar(100) NOT NULL COMMENT '이메일',
  `r_title` varchar(100) NOT NULL COMMENT '문의제목',
  `r_content` text DEFAULT NULL COMMENT '내용',
  `r_admcontent` text DEFAULT NULL COMMENT '관리자 답변 내용',
  `r_status` enum('답변대기','답변완료') DEFAULT '답변대기' COMMENT '내용',
  `r_file` varchar(50) DEFAULT NULL COMMENT '등록한 파일 - zip 파일만 허용함',
  `r_rdate` datetime DEFAULT NULL COMMENT '등록일',
  `r_admdate` datetime DEFAULT NULL COMMENT '관리자 등록일',
  `r_menu` varchar(50) NOT NULL DEFAULT '1:1문의' COMMENT '메뉴종류 - 1:1문의, 제휴문의',
  PRIMARY KEY (`r_uid`),
  KEY `r_inid` (`r_inid`),
  KEY `r_menu` (`r_menu`),
  KEY `r_status` (`r_status`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='문의관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_request`
--

LOCK TABLES `smart_request` WRITE;
/*!40000 ALTER TABLE `smart_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_secure_token`
--

DROP TABLE IF EXISTS `smart_secure_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_secure_token` (
  `st_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `st_type` varchar(50) DEFAULT NULL COMMENT '타입',
  `st_key` varchar(255) DEFAULT NULL COMMENT '키값',
  `st_checksum` varchar(255) DEFAULT NULL COMMENT '체크섬(체크가 필요한 값을 추가로 넣어준다)',
  `st_udate` datetime DEFAULT NULL COMMENT '업데이트(여유분필드)',
  `st_rdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`st_uid`) USING BTREE,
  UNIQUE KEY `st_type_st_key` (`st_type`,`st_key`) USING BTREE,
  KEY `st_type` (`st_type`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_secure_token`
--

LOCK TABLES `smart_secure_token` WRITE;
/*!40000 ALTER TABLE `smart_secure_token` DISABLE KEYS */;
INSERT INTO `smart_secure_token` VALUES (5,'totalAdminLogin','c7b938fdd2eaa3467991973d311ae360','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-29 19:27:07'),(2,'totalAdminLogin','032aa7aab8e6cbab3be5717fa61ba550','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-29 18:17:55'),(3,'totalAdminLogin','51407bad46592c0f7b69fd0f56a200b5','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-29 18:22:08'),(6,'totalAdminLogin','13ec57ab4b8cc3bd3d492504e21c3e4d','544afcfc0188aabe70b169e42d40d3f6',NULL,'2023-03-29 21:54:52'),(7,'totalAdminLogin','ad6316dfa335b8c82198678414e763e1','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 08:46:27'),(14,'totalAdminLogin','f80b74cc4cac8066b2ad8762549c89fa','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 09:05:44'),(10,'totalAdminLogin','75f9607118798773653008b497a9f1cf','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 08:47:50'),(11,'totalAdminLogin','627f52d5d9b7064b3254ccb3a972486a','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 08:58:45'),(12,'totalAdminLogin','c8ef9d92e7a4cac012261c793c41a7d2','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 08:59:00'),(13,'totalAdminLogin','328e8fe84b503af1d124c2e02c586570','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 08:59:01'),(15,'totalAdminLogin','14cb91ec94adcd18b38a08aff0284489','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 09:45:23'),(16,'totalAdminLogin','276773e1adf026d8772790289de8f634','5fa133e48396d93b13576af3e359e457',NULL,'2023-03-30 09:52:08'),(17,'login','0b0aab014c330b09166d9e1f6f6fb920','b74b1ea0fd5fe986ec96d95fccb5e671',NULL,'2023-03-30 09:52:34'),(18,'totalAdminLogin','bd724ca3f457fe1f94830139c7bb923f','4977e8e73dca30a4f9b7895eb491a99b',NULL,'2023-03-30 09:56:07'),(19,'totalAdminLogin','d368498754e3c24a4a56ef05e5c96d76','1d69ddb505ab7207dfadf9ca3cfa527c',NULL,'2023-03-30 09:58:41'),(20,'totalAdminLogin','00dce0b44f37d9557479a36b60e1ed07','74d3508965d5e1aaf0c1a3d443a431b9',NULL,'2023-03-30 10:00:30'),(21,'totalAdminLogin','01f5ff0512041ebf0d448368eabc910e','5e1476b22901dde182a6deb885c67d2b',NULL,'2023-03-30 10:12:20'),(22,'totalAdminLogin','087e99dc89418a82d52cb89c3988d0d2','6d8a60ccd85f2ce88b24bf81c0c034c8',NULL,'2023-03-30 10:34:06'),(23,'totalAdminLogin','333415f7bdd0dc2599fe92276306ca57','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 10:44:00'),(24,'totalAdminLogin','b2654a19688f1f41a2e961abad192026','4977e8e73dca30a4f9b7895eb491a99b',NULL,'2023-03-30 10:46:44'),(25,'totalAdminLogin','806814509ebd1f9795380164344ae185','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 10:53:30'),(26,'totalAdminLogin','ff811a97f004b0c785c38a844b768db5','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 11:29:53'),(27,'totalAdminLogin','20ad6fc3234594b8fd995f2732e9424c','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 11:51:01'),(30,'totalAdminLogin','be82da5165cc138cb7cd759154cd1e15','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 12:03:27'),(31,'totalAdminLogin','e693548ad91e38d689f0d1b55b51f07a','088d90f7bbbe8bbdae742d7ae513ec5e',NULL,'2023-03-30 12:16:00'),(32,'totalAdminLogin','3624ff6f7bc4f0c4b4be8cc41a4d0c2b','eb86aecfb14484bd800068f8d1421ed9',NULL,'2023-03-30 13:40:38'),(33,'totalAdminLogin','c89e679a51f4d1b8998174dc604d2be9','eb86aecfb14484bd800068f8d1421ed9',NULL,'2023-03-30 14:25:15'),(34,'totalAdminLogin','853c3c27f5931ce47581cd730da40fa6','eb86aecfb14484bd800068f8d1421ed9',NULL,'2023-03-30 14:41:03'),(35,'totalAdminLogin','902e20cd8646abaed1f82bef690734dd','eb86aecfb14484bd800068f8d1421ed9',NULL,'2023-03-30 15:30:06'),(36,'totalAdminLogin','ce37718567233908e7b17621b860455e','eb86aecfb14484bd800068f8d1421ed9',NULL,'2023-04-07 09:14:36'),(39,'totalAdminLogin','b8d9e42d2d32c3045a480678d518be67','79b31c366cbd0b7d05f6a1a4a279f492',NULL,'2023-04-11 09:27:13'),(40,'totalAdminLogin','81fc6d14df8565c034afebdac9607daf','79b31c366cbd0b7d05f6a1a4a279f492',NULL,'2023-04-12 12:03:22'),(41,'totalAdminLogin','9bb82b4818d8e1191f2791b932f8cd89','79b31c366cbd0b7d05f6a1a4a279f492',NULL,'2023-04-13 09:15:59'),(42,'totalAdminLogin','8156531fde433a759e43a4ea1c725449','e9208f20761fb04660b6810a69abf7c6',NULL,'2023-05-11 11:54:19'),(43,'totalAdminLogin','ccb8daec912effd38c0b7c1cabcc03fc','e9208f20761fb04660b6810a69abf7c6',NULL,'2023-05-18 10:24:41'),(44,'totalAdminLogin','b803d936e437dbca11f9aa432417adf3','e9208f20761fb04660b6810a69abf7c6',NULL,'2023-05-24 11:23:43'),(45,'totalAdminLogin','1451bab46b594bb7922708d5eeeae385','e9208f20761fb04660b6810a69abf7c6',NULL,'2023-06-01 11:05:10'),(46,'totalAdminLogin','e039047816ed21a883dd44b0a1a342ce','e9208f20761fb04660b6810a69abf7c6',NULL,'2023-06-01 11:05:30'),(47,'totalAdminLogin','c08f240f77cc19092c942187ce5234ae','d588ca7d13ba6df232ab71703f90bc3b',NULL,'2023-06-01 13:17:53'),(48,'login','154a6431ea98429160dddd49e42736c7','d96a76bf63001012a257c73c60edf2da',NULL,'2023-06-09 14:05:39'),(49,'totalAdminLogin','40217ca6ce4c3ef96bfa2d02cc87b5e7','d588ca7d13ba6df232ab71703f90bc3b',NULL,'2023-06-13 11:59:04'),(50,'totalAdminLogin','e64f6026505992ff534264ef307cb1c0','d588ca7d13ba6df232ab71703f90bc3b',NULL,'2023-06-14 09:43:33'),(51,'totalAdminLogin','c186e804a62303dda4b392e4cc26719d','d588ca7d13ba6df232ab71703f90bc3b',NULL,'2023-06-14 10:24:33'),(52,'totalAdminLogin','47bbd444a66342e74dde04778544f8dc','d588ca7d13ba6df232ab71703f90bc3b',NULL,'2023-06-15 11:49:50'),(53,'totalAdminLogin','f959b751c5512c8cb942cc62f87e5419','d588ca7d13ba6df232ab71703f90bc3b',NULL,'2023-06-16 17:46:41'),(54,'totalAdminLogin','1819425c6156d7955edfdded87b59f3c','8825ab1cf8a239fa64e4e7d59ebcfd38',NULL,'2023-08-01 10:24:16'),(55,'totalAdminLogin','b52442b332ec88e2e05ac2e0ad484c68','8825ab1cf8a239fa64e4e7d59ebcfd38',NULL,'2023-08-04 17:36:37'),(56,'totalAdminLogin','7d30d06caf7b62c4acef92a70ca9cc73','8825ab1cf8a239fa64e4e7d59ebcfd38',NULL,'2023-08-07 11:11:04'),(57,'totalAdminLogin','0cdfb6295576303e13745e02fbb6d789','8825ab1cf8a239fa64e4e7d59ebcfd38',NULL,'2023-08-10 16:22:05'),(58,'totalAdminLogin','bc935afa58ae04ddde1d90bf4492d2c0','6d64afa208c9119ff0dfe8505c4049d2',NULL,'2023-08-23 18:16:57'),(59,'totalAdminLogin','7e30c2da091888cbd5cdb32c1acce501','6d64afa208c9119ff0dfe8505c4049d2',NULL,'2023-09-04 18:19:32'),(60,'totalAdminLogin','348a1bcdb3fe8fa168cbc258313f06b4','6d64afa208c9119ff0dfe8505c4049d2',NULL,'2023-09-04 18:19:33'),(61,'totalAdminLogin','a58e32496ae9bd86b9e439f6d97711d3','6d64afa208c9119ff0dfe8505c4049d2',NULL,'2023-09-05 10:53:55'),(62,'totalAdminLogin','033517de14692f3e784fbfec4509a4a7','6d64afa208c9119ff0dfe8505c4049d2',NULL,'2023-09-13 15:35:03'),(63,'login','d287761575de00eb8d1ae48c1d9993b6','35ed75f4ceabed36770aec57a2f796fe',NULL,'2023-09-14 16:08:13'),(64,'totalAdminLogin','4ef06fd2fe6021d1df4e4e19e4df89fd','6d64afa208c9119ff0dfe8505c4049d2',NULL,'2023-09-14 16:08:40'),(65,'totalAdminLogin','569d68a7157f42496fb4da48d5fb763b','38ffb86ecdb73b7fa87c2c726f9e29b2',NULL,'2023-09-19 09:53:49'),(66,'login','3a0bdc8fdf6ea4300388c147f70f211b','35ed75f4ceabed36770aec57a2f796fe',NULL,'2023-09-19 10:23:19'),(67,'totalAdminLogin','7963983d1f210252a0e1a75ff301f167','6d64afa208c9119ff0dfe8505c4049d2',NULL,'2023-09-19 10:23:45'),(68,'totalAdminLogin','f0694a62eaa563973396d7e2ad87db64','41d394ca33a5913f2b208f20b86469b2',NULL,'2023-10-31 17:58:27'),(69,'totalAdminLogin','1a0f3ede07462cb8f120b5108886dbb8','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-11-07 14:10:53'),(70,'totalAdminLogin','08d4c005224111762622c02ad868e800','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-11-15 17:03:40'),(71,'totalAdminLogin','40de469829d88b8f133b1a8ebe3afd43','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-11-15 17:10:11'),(72,'totalAdminLogin','217202112cf67a3391350e8adeda2766','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-11-15 17:10:57'),(73,'totalAdminLogin','a9f6e8b603e19864b1816b029f356ae6','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-11-20 14:50:00'),(74,'totalAdminLogin','aea37a230e2e7841f42dd134ee01c31e','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-11-23 09:45:22'),(75,'totalAdminLogin','9019d40996d321446bbe57cf0f6b4b22','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-11-24 15:53:45'),(76,'totalAdminLogin','73c15e3aa24163a999418a720c764b71','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-11-29 18:00:16'),(77,'totalAdminLogin','10ea2b5635c3401e61418d3b9ddbca28','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-12-01 17:40:00'),(78,'totalAdminLogin','1221612dcad556cca8fafcc0af230fda','1ec23d13bdb14fd755fd082293cfe90f',NULL,'2023-12-05 10:12:07'),(79,'totalAdminLogin','defd710c411d340723c6f3a90402ff01','cb78bb2c0aa2052c5995be436ecd2ef3',NULL,'2023-12-19 09:32:42'),(80,'totalAdminLogin','d07259d72585c65e00b1e39fcc9a0db8','cb78bb2c0aa2052c5995be436ecd2ef3',NULL,'2023-12-21 13:47:04'),(81,'totalAdminLogin','d9bad525d447da3967db1d5be7f8687e','cb78bb2c0aa2052c5995be436ecd2ef3',NULL,'2024-01-22 09:53:17'),(82,'totalAdminLogin','989e648b2268d15e6d2a8fff5753fd88','cb78bb2c0aa2052c5995be436ecd2ef3',NULL,'2024-01-22 14:08:33'),(83,'totalAdminLogin','7d0923dab1c435035bf9d90e737930ee','cb78bb2c0aa2052c5995be436ecd2ef3',NULL,'2024-01-25 10:30:39'),(84,'totalAdminLogin','8e68f2ac4ea0c84002e0d1340dcb9e8e','cb78bb2c0aa2052c5995be436ecd2ef3',NULL,'2024-01-25 14:22:07'),(85,'totalAdminLogin','b9dfe3da5336f05b3c288181be6c94cf','46606f0b4ecdb44efa1dc71a3086f6a3',NULL,'2024-02-08 10:54:34'),(86,'totalAdminLogin','b18eebeea20886115268455f52161acd','46606f0b4ecdb44efa1dc71a3086f6a3',NULL,'2024-02-14 16:00:50'),(87,'totalAdminLogin','34aed37fae1c8e8483127d875bb00d8c','46606f0b4ecdb44efa1dc71a3086f6a3',NULL,'2024-02-27 10:21:05');
/*!40000 ALTER TABLE `smart_secure_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_setup`
--

DROP TABLE IF EXISTS `smart_setup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_setup` (
  `s_uid` int(11) NOT NULL,
  `s_adid` varchar(20) NOT NULL DEFAULT '' COMMENT '관리자 아이디',
  `s_adpwd` varchar(50) NOT NULL DEFAULT '' COMMENT '관리자 패스워드',
  `s_glbtel` varchar(50) NOT NULL DEFAULT '' COMMENT '고객센터 대표번호',
  `s_glbmanagerhp` varchar(50) NOT NULL DEFAULT '' COMMENT '운영자 핸드폰',
  `s_smsid` varchar(20) NOT NULL DEFAULT '' COMMENT 'SMS 아이디',
  `s_smspw` varchar(255) NOT NULL COMMENT 'SMS 패스워드',
  `s_ademail` varchar(50) NOT NULL DEFAULT '' COMMENT '관리자 이메일',
  `s_adshop` varchar(50) NOT NULL DEFAULT '' COMMENT '사이트명',
  `s_glbtlt` varchar(255) NOT NULL DEFAULT '' COMMENT 'head - 타이틀',
  `s_glbdsc` varchar(255) NOT NULL DEFAULT '' COMMENT 'head - 설명',
  `s_glbkwd` varchar(255) NOT NULL DEFAULT '' COMMENT 'head - 키워드',
  `s_gmeta` text NOT NULL COMMENT '메타태그',
  `s_cs_info` text NOT NULL COMMENT '고객센터운영시간',
  `s_pointusevalue` int(11) NOT NULL DEFAULT 0 COMMENT '적립금 사용가능액( 00원부터 사용가능 )',
  `s_pointuselimit` int(11) NOT NULL DEFAULT 0 COMMENT '주문건당 적립금 사용제한금액',
  `s_joinpoint` int(11) NOT NULL DEFAULT 0 COMMENT '회원가입시 지급할 적립금',
  `s_joinpointprodate` int(11) NOT NULL DEFAULT 0 COMMENT '회원가입시 적립금 지급일',
  `s_orderpointprodate` int(11) NOT NULL DEFAULT 0 COMMENT '상품주문시 적립금 지급일',
  `s_productevalpoint` int(11) NOT NULL COMMENT '포토후기 적립금',
  `s_productevalprodate` int(11) NOT NULL COMMENT '포토후기 적립금 지급일',
  `s_pg_type` varchar(50) NOT NULL COMMENT 'pg사명( var.php 에서 지정 )',
  `s_pg_code` varchar(50) NOT NULL COMMENT 'pg사 코드',
  `s_pg_code_escrow` varchar(100) NOT NULL,
  `s_pg_key` varchar(50) NOT NULL COMMENT 'pg사 키',
  `s_pg_enc_key` varchar(50) DEFAULT NULL COMMENT '암호화 연동 key',
  `s_pg_mode` enum('service','test') NOT NULL DEFAULT 'test' COMMENT 'pg 실결제여부 (lgpay  만 해당됨)',
  `s_pg_virtual_date` int(11) NOT NULL DEFAULT 10 COMMENT '가상계좌 입금기한 (일)',
  `s_delprice` int(11) NOT NULL DEFAULT 0 COMMENT '기본배송비',
  `s_delprice_free` int(11) NOT NULL DEFAULT 0 COMMENT '무료배송비',
  `s_del_company` varchar(100) NOT NULL DEFAULT '' COMMENT '지정택배사',
  `s_del_date` varchar(100) NOT NULL DEFAULT '' COMMENT '평균배송기간',
  `s_del_complain_price` varchar(100) NOT NULL DEFAULT '' COMMENT '반송배송비',
  `s_del_return_addr` varchar(100) NOT NULL DEFAULT '' COMMENT '반송주소',
  `s_complain_ok` text NOT NULL COMMENT '교환/반품 가능한경우',
  `s_complain_fail` text NOT NULL COMMENT '교환/반품 불 가능한경우',
  `s_ceo_name` varchar(50) NOT NULL COMMENT '대표자명',
  `s_company_name` varchar(50) NOT NULL COMMENT '회사명',
  `s_company_num` varchar(50) NOT NULL COMMENT '사업자등록번호',
  `s_company_snum` varchar(50) NOT NULL COMMENT '통신판매신고번호',
  `s_company_addr` varchar(100) NOT NULL COMMENT '회사주소지',
  `s_fax` varchar(20) NOT NULL COMMENT '팩스번호',
  `s_privacy_name` varchar(20) NOT NULL COMMENT '개인정보관리책임자',
  `s_view_network_company_info` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '사업자정보 노출 여부',
  `s_view_escrow_join_info` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '에스크로 가입팝업 노출 여부',
  `s_mailid` varchar(50) NOT NULL DEFAULT '' COMMENT '이메일 아이디',
  `s_mailpw` varchar(50) NOT NULL DEFAULT '' COMMENT '이메일 비번',
  `s_mailuse` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '이메일 활성화여부',
  `s_facebook_login_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '페이스북 로그인 사용여부',
  `s_facebook_key` varchar(50) NOT NULL DEFAULT '' COMMENT 'facebook api key',
  `s_facebook_secret` varchar(100) NOT NULL COMMENT '페이스북 앱 시크릿',
  `s_display_main_new_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '메인 신상품 노출여부',
  `s_display_main_new_col` int(11) NOT NULL DEFAULT 4 COMMENT '메인 신상품 col 갯수',
  `s_display_main_new_row` int(11) NOT NULL DEFAULT 2 COMMENT '메인 신상품 row 갯수',
  `s_display_main_new_title_img` varchar(50) NOT NULL DEFAULT '' COMMENT '메인 신상품 타이틀 이미지',
  `s_display_main_best_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '메인 베스트상품 노출여부',
  `s_display_main_best_col` int(11) NOT NULL DEFAULT 4 COMMENT '메인 베스트상품 col 갯수',
  `s_display_main_best_row` int(11) NOT NULL DEFAULT 2 COMMENT '메인 베스트상품 row 갯수',
  `s_display_main_best_title_img` varchar(50) NOT NULL DEFAULT '' COMMENT '메인 베스트상품  타이틀 이미지',
  `s_display_main_sale_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '메인 세일상품 노출여부',
  `s_display_main_sale_col` int(11) NOT NULL DEFAULT 4 COMMENT '메인 세일상품 col 갯수',
  `s_display_main_sale_row` int(11) NOT NULL DEFAULT 2 COMMENT '메인 세일상품 row 갯수',
  `s_display_main_sale_title_img` varchar(50) NOT NULL DEFAULT '' COMMENT '메인 세일상품  타이틀 이미지',
  `s_display_list_style` enum('thumb','list') NOT NULL DEFAULT 'thumb' COMMENT '목록페이지 기본 스타일',
  `s_display_list_thumb_col` int(11) NOT NULL DEFAULT 4 COMMENT '목록페이지 섬네일  col 갯수',
  `s_display_list_thumb_row` int(11) NOT NULL DEFAULT 5 COMMENT '목록페이지 섬네일  row 갯수',
  `s_join_auth_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '본인인증 서비스 사용여부',
  `s_join_auth_kcb_code` varchar(20) NOT NULL DEFAULT '' COMMENT '본인인증 서비스 kcb 회원사 코드',
  `s_license` varchar(100) DEFAULT NULL COMMENT '원데이넷 발급 라이센스번호 - 삭제 변형불가',
  `s_account_commission` float NOT NULL DEFAULT 0 COMMENT '업체정산 수수료',
  `s_account_commission_type` enum('persent','supplyprice') NOT NULL DEFAULT 'persent' COMMENT '업체정산 유형',
  `s_sitename` varchar(50) DEFAULT NULL COMMENT '사이트명',
  `s_skin` varchar(100) NOT NULL DEFAULT 'default' COMMENT '스킨 폴더명',
  `s_skin_m` varchar(100) NOT NULL,
  `s_login_page_phone` varchar(20) NOT NULL COMMENT '관리자로그인 페이지에 표시될 전화번호',
  `s_login_page_email` varchar(100) NOT NULL COMMENT '관리자로그인 페이지에 표시될 이메일',
  `kakao_login_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '카카오로그인 사용여부',
  `kakao_api` varchar(100) NOT NULL COMMENT '카카오 API',
  `kakao_js_api` varchar(255) NOT NULL COMMENT '카카오 자바스크립트 api키',
  `recaptcha_api` varchar(100) DEFAULT NULL COMMENT '스팸방지 구글 API - site key',
  `recaptcha_secret` varchar(100) DEFAULT NULL COMMENT '스팸방지 구글 API - Secret key',
  `s_naver_switch` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '네이버 EP 전체적용 설정 ',
  `s_daum_switch` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '다음 EP 전체적용 설정 ',
  `npay_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '네이버페이 사용여부',
  `npay_mode` enum('test','real') NOT NULL DEFAULT 'test' COMMENT '네이버페이 활성화 모드',
  `npay_id` varchar(255) NOT NULL COMMENT '네이버페이 아이디',
  `npay_all_key` varchar(255) NOT NULL COMMENT '네이버공통인증키',
  `npay_key` varchar(255) NOT NULL COMMENT '네이버페이 가맹점인증키',
  `npay_bt_key` varchar(255) NOT NULL COMMENT '네이버페이 버튼 인증키',
  `npay_sync_mode` enum('test','real') NOT NULL DEFAULT 'test' COMMENT '네이버페이 주문연동 모드',
  `npay_lisense` varchar(255) NOT NULL COMMENT '네이버페이 주문연동 라이센스키',
  `npay_secret` varchar(255) NOT NULL COMMENT '네이버페이 주문연동 비밀키',
  `member_sleep_period` int(11) NOT NULL DEFAULT 12 COMMENT '휴면계정전환일수',
  `member_cpw_period` int(11) NOT NULL DEFAULT 3 COMMENT '비밀번호 갱신 안내 주기',
  `member_login_cnt` int(11) NOT NULL DEFAULT 5 COMMENT '로그인 시도 횟수 ',
  `s_product_list_hashtag_cnt` int(11) NOT NULL COMMENT '상품리스트 해시태그 노출 개수',
  `s_product_list_hashtag_shuffle` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '상품리스트 해시태그 노출 방식 (Y: 무작위, N: 빈도수)',
  `s_product_list_hashtag_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '상품리스트 해시태그 노출 여부 (Y: 노출, N: 비노출)',
  `s_product_auto_C` int(3) NOT NULL DEFAULT 0 COMMENT '배송상품자동정산전환(카드)',
  `s_product_auto_L` int(3) NOT NULL DEFAULT 0 COMMENT '배송상품자동정산전환(실시간계좌이체)',
  `s_product_auto_B` int(3) NOT NULL DEFAULT 0 COMMENT '배송상품자동정산전환(무통장입금)',
  `s_product_auto_G` int(3) NOT NULL DEFAULT 0 COMMENT '배송상품자동정산전환(전액포인트)',
  `s_product_auto_V` int(3) NOT NULL DEFAULT 0 COMMENT '배송상품자동정산전환(가상계좌)',
  `s_product_auto_H` int(3) NOT NULL DEFAULT 0 COMMENT '배송상품자동정산전환(휴대폰)',
  `s_product_auto_P` int(3) NOT NULL DEFAULT 0 COMMENT '배송상품자동정산전환(페이코)',
  `s_product_auto_on` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '배송상품 정산 자동처리 사용여부',
  `s_product_auto_PP` int(3) NOT NULL DEFAULT 0 COMMENT '배송상품 페이플 간편결제 자동정산 처리 일수 추가',
  `s_item1` varchar(100) NOT NULL COMMENT '업태',
  `s_item2` varchar(100) NOT NULL COMMENT '종목',
  `TAX_MODE` enum('service','test') NOT NULL DEFAULT 'test' COMMENT '세금계산서 - 테스트 여부',
  `TAX_CERTKEY` varchar(50) NOT NULL COMMENT '바로빌 CERTKEY',
  `TAX_CHK` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '바로빌 세금계산서 사용여부',
  `TAX_BAROBILL_ID` varchar(50) NOT NULL COMMENT '세금계산서 - 바로빌 아이디',
  `TAX_BAROBILL_NAME` varchar(50) NOT NULL COMMENT '세금계산서 - 바로빌 가입자명',
  `TAX_BAROBILL_PW` varchar(50) NOT NULL COMMENT '세금계산서 - 바로빌 비밀번호',
  `join_birth` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '회원가입 생일 노출',
  `join_birth_required` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '회원가입 생일 필수',
  `join_sex` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '회원가입 성별 노출',
  `join_sex_required` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '회원가입 성별 필수',
  `join_tel` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '회원가입 전화 노출',
  `join_tel_required` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '회원가입 전화 필수',
  `join_addr` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '회원가입 주소 노출',
  `join_addr_required` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '회원가입 주소 필수',
  `join_approve` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '가입승인(Y: 자동승인, N: 승인후가입)',
  `join_ban_id` text NOT NULL COMMENT '가입제한 아이디',
  `join_id_limit_min` int(2) NOT NULL DEFAULT 4 COMMENT '아이디 최소 글자수',
  `join_id_limit_max` int(2) NOT NULL DEFAULT 0 COMMENT '아이디 최대 글자수',
  `join_pw_limit_min` int(2) NOT NULL DEFAULT 4 COMMENT '비밀번호 최소 글자수',
  `join_pw_limit_max` int(2) NOT NULL DEFAULT 0 COMMENT '비밀번호 최대 글자수',
  `join_pw_sp_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '특수문자 혼용 필수(Y: 사용, N: 미사용)',
  `join_pw_sp_length` int(2) NOT NULL DEFAULT 0 COMMENT '특수문자 혼용 글자수',
  `join_pw_up_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '대문자 혼용 필수(Y: 사용, N: 미사용)',
  `join_pw_up_length` int(2) NOT NULL DEFAULT 0 COMMENT '대문자 혼용 글자수',
  `join_email_list` text NOT NULL COMMENT '가입 이메일 기본 리스트',
  `join_spam` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '스팸방지 사용여부(Y: 사용, N: 미사용)',
  `s_daily` date NOT NULL COMMENT '오늘의 날짜',
  `s_set_email_txt` text NOT NULL COMMENT '이메일 수신동의 및 수신거미에 대한 문구',
  `s_deny_tel` varchar(20) DEFAULT NULL COMMENT '080 수신거부 신청전화번호',
  `s_deny_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '080 수신거부 사용여부',
  `s_2year_opt_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '매 2년마다 수신동의 설정',
  `s_2year_opt_content_top` text DEFAULT NULL COMMENT '매 2년마다 수신동의 메일 - 상단내용',
  `s_2year_opt_title` varchar(200) DEFAULT NULL COMMENT '매 2년마다 수신동의 메일 - 타이틀',
  `s_pg_skey` varchar(512) NOT NULL COMMENT '이니시스 웹표준모듈 사인키',
  `s_pg_escrow_skey` varchar(512) NOT NULL COMMENT '에스크로전용 사인키',
  `s_del_addprice_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '추가배송비적용여부',
  `s_del_addprice_use_normal` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '일반배송상품을 무료배송비 이상 구매하여 무료배송이 적용된경우 추가배송비 적용여부',
  `s_del_addprice_use_product` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상품별배송 상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비 적용여부',
  `s_del_addprice_use_unit` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '개별배송상품의 추가배송비 적용여부',
  `s_del_addprice_use_free` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '무료배송상품의 추가배송비 적용여부',
  `s_vat_product` enum('Y','N','C') NOT NULL DEFAULT 'Y' COMMENT '상품 부가세 설정여부 - Y:과세, N:면세, C: 복합과세',
  `s_vat_delivery` enum('Y','N','C') NOT NULL DEFAULT 'Y' COMMENT '배송비 부가세 설정여부 - Y:과세, N:면세, C: 복합과세',
  `s_vat_discount` enum('Y','N','D') NOT NULL DEFAULT 'Y' COMMENT '할인액의 과세여부 - Y:과세부터 차감, N:면세부터 차감, D:비율로 차감',
  `s_glb_mall_used_en` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '영문몰 사용여부 ',
  `s_glb_mall_addcurr_en` enum('NO','KRW','USD','JPY','CNY') NOT NULL DEFAULT 'NO' COMMENT '영문몰 추가화폐',
  `s_glb_mall_base_delivery_country_en` varchar(10) NOT NULL COMMENT '영문몰 기본배송지역 국가코드',
  `s_glb_mall_used_cn` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '중문몰 사용여부',
  `s_glb_mall_addcurr_cn` enum('NO','KRW','USD','JPY','CNY') NOT NULL DEFAULT 'NO' COMMENT '중문몰 추가화폐',
  `s_glb_mall_base_delivery_country_cn` varchar(10) NOT NULL COMMENT '중문몰 기본배송지역 국가코드',
  `s_glb_mall_used_jp` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '일문몰 사용여부',
  `s_glb_mall_addcurr_jp` enum('NO','KRW','USD','JPY','CNY') NOT NULL DEFAULT 'NO' COMMENT '일문몰 추가화폐',
  `s_glb_mall_base_delivery_country_jp` varchar(10) NOT NULL COMMENT '일문몰 기본배송지역 국가코드',
  `s_ssl_check` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '보안서버 사용여부',
  `s_ssl_status` enum('대기','진행','만료') NOT NULL DEFAULT '대기' COMMENT '보안서버 진행상태 - 대기 , 진행 , 만료',
  `s_ssl_sdate` date NOT NULL COMMENT '보안서버 사용기간 - 시작일',
  `s_ssl_edate` date NOT NULL COMMENT '보안서버 사용기간 - 종료일',
  `s_ssl_domain` varchar(50) NOT NULL COMMENT '보안서버 도메인',
  `s_ssl_port` varchar(10) NOT NULL COMMENT '보안서버 포트번호',
  `s_ssl_admin_loc` enum('N','A','P') NOT NULL COMMENT '관리자 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지',
  `s_ssl_admin_page` text NOT NULL COMMENT '관리자 보안서버 추가 적용페이지',
  `s_ssl_m_loc` enum('N','A','P') NOT NULL COMMENT '모바일 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지',
  `s_ssl_m_page` text NOT NULL COMMENT '모바일 보안서버 추가 적용페이지',
  `s_ssl_pc_loc` enum('N','A','P') NOT NULL COMMENT 'PC 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지',
  `s_ssl_pc_page` text NOT NULL COMMENT 'PC 보안서버 추가 적용페이지',
  `s_ssl_pc_img` enum('N','U','K','A','C','E') NOT NULL DEFAULT 'N' COMMENT 'PC 보안서버 인증이미지 - N:미사용, U:UCERT SSL , K:KISA SSL, A:Alpha SSL, C:Comodo SSL, E:기타',
  `s_ssl_pc_img_etc` text NOT NULL COMMENT 'PC 보안서버 기타 인증이미지 소스',
  `s_ssl_pc_sealnum` varchar(100) NOT NULL COMMENT 'PC 보안서버 Seal Number',
  `s_ssl_pc_sealid` varchar(100) NOT NULL COMMENT 'PC 보안서버 Seal ID',
  `s_currency_auto` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '환율정보 자동 수집 사용여부',
  `s_information_use_pc` text NOT NULL COMMENT '이용안내 PC',
  `s_information_use_mobile` text NOT NULL COMMENT '이용안내 Moble',
  `s_leave_guidance` text NOT NULL COMMENT '회원탈퇴 주의사항',
  `nv_login_use` enum('Y','N') NOT NULL COMMENT '네이버 로그인 사용여부',
  `nv_login_key` varchar(50) NOT NULL COMMENT '네이버 로그인 ClientID',
  `nv_login_secret` varchar(50) NOT NULL COMMENT '네이버 로그인 ClientSecret',
  `instagram_main_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '인스타그램 메인 노출 여부(Y: 노출, N: 비노출)',
  `instagram_id` varchar(50) NOT NULL COMMENT '인스타그램 아이디',
  `s_device_mode` enum('A','P','M') NOT NULL DEFAULT 'A' COMMENT '디바이스 선택(A: 전체, P: pc만, M: Mobile만)',
  `s_favicon` varchar(100) NOT NULL COMMENT 'PC 파비콘 설정',
  `s_home_icon` varchar(100) NOT NULL COMMENT 'Mobile 홈 아이콘',
  `s_find_pw_email` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '비밀번호 찾기 이메일 사용여부',
  `s_find_pw_sms` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '비밀번호 찾기 휴대전화 사용여부',
  `daily_update_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '데일리 업데이트 날짜',
  `app_time` varchar(20) NOT NULL COMMENT '설정 시간',
  `s_pg_paymethod_B` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '무통장입금 사용여부',
  `s_pg_paymethod_C` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '신용카드 사용여부',
  `s_pg_paymethod_L` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '실시간 계좌이체 사용여부',
  `s_pg_paymethod_V` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '가상계좌 사용여부',
  `s_pg_paymethod_H` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '휴대폰 결제 사용여부',
  `s_group_name` varchar(50) NOT NULL DEFAULT '등급' COMMENT '등급 표기명칭',
  `s_today_view_time` int(11) NOT NULL DEFAULT 24 COMMENT '최근본상품 유지시간',
  `s_today_view_max` int(11) NOT NULL DEFAULT 20 COMMENT '최근본상품 최대수량',
  `s_bank_autocheck_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '무통장 자동입금 확인 서비스 사용여부',
  `s_apibox_id` varchar(100) NOT NULL COMMENT 'apibox 아이디',
  `s_online_notice_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '미확인입금자페이지 노출여부',
  `s_online_notice_auto` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '실시간입금 확인 시 미확인 입금자 확인목록에 자동등록 사용여부',
  `s_online_notice_privacy` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '미확인 입금확인 목록 노출시 입금자명 **처리',
  `s_online_notice_bank` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '미확인 입금확인 목록 노출시 입금은행 항목 노출여부',
  `s_online_notice_view` tinyint(4) NOT NULL DEFAULT 3 COMMENT '미확인 입금확인 목록에 노출되는 기간, 입금일 기준',
  `promotion_event_delivery_config` text DEFAULT NULL COMMENT '무료배송설정(serialize형식) - ',
  `sns_link_instagram` varchar(255) NOT NULL COMMENT '소셜미디어 링크(인스타그램)',
  `sns_link_facebook` varchar(255) NOT NULL COMMENT '소셜미디어 링크(페이스북)',
  `sns_link_twitter` varchar(255) NOT NULL COMMENT '소셜미디어 링크(트위터)',
  `sns_link_blog` varchar(255) NOT NULL COMMENT '소셜미디어 링크(블로그)',
  `sns_link_cafe` varchar(255) NOT NULL COMMENT '소셜미디어 링크(카페)',
  `sns_link_youtube` varchar(255) NOT NULL COMMENT '소셜미디어 링크(유튜브)',
  `sns_link_kkp` varchar(255) NOT NULL COMMENT '소셜미디어 링크(카카오 플러스 친구)',
  `sns_link_kks` varchar(255) NOT NULL COMMENT '소셜미디어 링크(카카오 스토리)',
  `s_recommend_keyword` text NOT NULL COMMENT '추천 검색 키워드',
  `s_recommend_hashtag` text NOT NULL COMMENT '인기 해시태그',
  `groupset_autouse` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '등급자동평가 사용유무(Y:자동평가사용,N:수동평가)',
  `groupset_apply_rdate` datetime NOT NULL COMMENT '등급평가 최근 갱신일',
  `groupset_auto_daily` enum('day','week','month') NOT NULL DEFAULT 'month' COMMENT '회원등급평가 기간설정 (매일,매주,매달)',
  `groupset_check_term` enum('monthlast','month1','month2','month3','month4','month5','month6') NOT NULL DEFAULT 'month3' COMMENT '회원등급평가 특정기간(지난달,최근1개월,최근2개월,최근3개월,최근4개월,최근5개월,최근6개월)',
  `member_return_groupinit` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '휴면해제 시 회원등급초기화 설정',
  `member_return_type` enum('login','auth') NOT NULL DEFAULT 'auth' COMMENT '휴면해제 방법 (login: 로그인 후 인증없이 휴면해제,auth:로그인 시 이메일 인증 후 휴면해제)',
  `s_display_pinfo_pc` varchar(255) NOT NULL COMMENT 'pc 노출항목 설정',
  `s_display_pinfo_mo` varchar(255) NOT NULL COMMENT 'mo 노출항목 설정',
  `s_display_pinfo_mo_use_pc` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'mo 노출항목 pc와 동일하게 적용',
  `s_display_pinfo_add` varchar(255) NOT NULL COMMENT '노출항목 추가설정',
  `s_display_pinfo_add_info` varchar(255) NOT NULL COMMENT '노출항목 설정 ( 정상가,적립금,상품쿠폰,회원혜택)',
  `s_display_relation_pc_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'pc 관련상품사용여부',
  `s_display_relation_pc_col` tinyint(2) NOT NULL DEFAULT 5 COMMENT 'pc 관련상품 한줄에 노출되는 상품수 ',
  `s_display_relation_pc_row` tinyint(2) NOT NULL DEFAULT 4 COMMENT 'pc 관련상품 최대 노출 줄수 ',
  `s_display_relation_mo_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'mo 관련상품사용여부',
  `s_display_relation_mo_col` tinyint(2) NOT NULL DEFAULT 3 COMMENT 'mo 관련상품 한줄에 노출되는 상품수 ',
  `s_display_relation_mo_row` tinyint(2) NOT NULL DEFAULT 4 COMMENT 'mo 관련상품 최대 노출 줄수 ',
  `s_main_review` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '메인 베스트리뷰 노출 설정(Y: 노출, N: 비노출)',
  `s_main_review_porder` enum('S','R') NOT NULL DEFAULT 'R' COMMENT '리뷰 상품 정렬 기준(S: 최신등록순, R:랜덤노출)',
  `s_main_review_score` int(1) NOT NULL DEFAULT 1 COMMENT '리뷰 평점 노출 제한 (1~5) 기본 1',
  `s_main_review_view` enum('A','P') NOT NULL DEFAULT 'A' COMMENT '리뷰 출력조건(A: 모든리뷰, P: 포토리뷰)',
  `s_main_review_limit` int(2) NOT NULL DEFAULT 5 COMMENT '메인리뷰 최대 출력 개수',
  `s_search_option` varchar(255) NOT NULL COMMENT '검색 :: 조건설정값(var.php)',
  `s_search_display` int(11) NOT NULL COMMENT '검색 :: (PC)상품 진열 설정',
  `s_search_mobile_display` int(11) NOT NULL COMMENT '검색 :: (모바일)상품 진열 설정',
  `s_search_diff_orderby` enum('salecnt','review') NOT NULL DEFAULT 'salecnt' COMMENT '검색 :: 다른고객이많이찾은상품 정렬방식',
  `s_search_diff_maxcnt` int(11) NOT NULL DEFAULT 20 COMMENT '검색 :: 다른고객이많이찾은상품 최대출력개수',
  `s_search_diff_option` enum('rand','normal') NOT NULL DEFAULT 'rand' COMMENT '검색 :: 다른고객이많이찾은상품 옵션',
  `s_bbs_forbidden_word` text NOT NULL COMMENT '게시판 금지어관리',
  `s_bbs_faq_type` text NOT NULL COMMENT 'FAQ 게시판 분류관리',
  `s_faq_keyword` text DEFAULT NULL COMMENT 'FAQ 고객센터 메인에 노출될 FAQ 인기 키워드',
  `s_pg_installment` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'PG사 할부 개월 수',
  `s_pg_installment_peroid` varchar(255) NOT NULL COMMENT '할부개월수 선택(최대 12까지 고정)',
  `s_pg_noinstallment` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '무이자 할부 설정',
  `s_pg_noinstallment_peroid` varchar(255) NOT NULL COMMENT '무이자 할부 상용 시 개월 수 정보',
  `s_pg_app_scheme` varchar(255) NOT NULL COMMENT '앱 스키마명',
  `s_pg_inicis_type` enum('D','W') NOT NULL DEFAULT 'D' COMMENT '이니시스 모듈선택(기본모듈D,웹표준모듈W)',
  `s_cash_receipt_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '현금영수증 사용유무',
  `s_cash_receipt_sel` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '현금영수증 필수 선택 여부',
  `s_cash_receipt_issued_type` enum('auto','admin') NOT NULL DEFAULT 'auto' COMMENT '현금영수증 발급방식(자동-auto,관리자-admin)',
  `payco_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '페이코 사용여부 ',
  `payco_mode` enum('test','service') NOT NULL DEFAULT 'test' COMMENT '페이코 활성화 여부',
  `payco_sellerkey` varchar(255) DEFAULT NULL COMMENT '페이코 가맹점 코드',
  `payco_cpid` varchar(255) DEFAULT NULL COMMENT '페이코 상점 ID',
  `payco_productid` varchar(255) DEFAULT NULL COMMENT '페이코 상품 ID',
  `payco_app_scheme` varchar(255) DEFAULT NULL COMMENT '페이코 app 스키마값',
  `payco_paymethod` varchar(255) NOT NULL COMMENT '페이코 결제수단 (,)로 구분',
  `s_pg_mobile_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '휴대폰 결제 사용여부',
  `s_pg_mobile_type` enum('pg','other') NOT NULL DEFAULT 'pg' COMMENT ' 휴대폰 결제 모듈 (pg,other)  기본-pg, 별도의 외부 모듈 - other :: 고정값 pg',
  `s_coupon_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '쿠폰사용여부 (Y사용,N)',
  `s_coupon_view` enum('all','member') NOT NULL DEFAULT 'all' COMMENT '쿠폰노출 설정 (all:전체,member:회원)',
  `s_coupon_ordercancel_return` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '주문취소에 따른 복원 사용여부 (Y:사용,N:미사용)',
  `s_pg_escrow_mark` varchar(50) NOT NULL COMMENT '에스크로 인증 마크',
  `s_promotion_attend_reay_pc` varchar(50) NOT NULL COMMENT '출석체크이벤트 준비중이미지(PC)',
  `s_promotion_attend_reay_mo` varchar(50) NOT NULL COMMENT '출석체크이벤트 준비중이미지(Mobile)',
  `facebook_share_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '페이스북 공유 사용여부',
  `kakao_share_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '카카오톡 공유 사용여부',
  `twitter_share_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '트위터 공유 사용여부',
  `pinter_share_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '핀터레스트 공유 사용여부',
  `insta_client` varchar(255) NOT NULL COMMENT '인스타그램 연동 클라이언트 아이디',
  `insta_token` varchar(255) NOT NULL COMMENT '인스타그램 연동 토큰 키',
  `npay_sync_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '최종 네이버페이 동기화 시간',
  `npay_callback` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '콜백 동작 마지막 시간',
  `s_producteval_limit` enum('Y','N','B') NOT NULL DEFAULT 'N' COMMENT '상품후기 작성조건(Y:상품을 구매한 회원만, N: 모든회원 작성가능, B: 상품을 구매한횟수만큼)',
  `s_paycancel_method` enum('B','D') NOT NULL DEFAULT 'B' COMMENT '결제 취소시 포인트 환불 방식',
  `s_share_favicon` varchar(100) NOT NULL COMMENT '공유시 파비콘 이미지',
  `s_none_member_buy` enum('Y','N') DEFAULT 'Y' COMMENT '회원, 비회원 구매 가능 여부',
  `s_none_member_login_skip` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '비회원 바로구매 시 로그인 페이지 경유 여부(Y-바로구매, N-로그인페이지 경유)',
  `s_order_auto_cancel_term` int(10) NOT NULL DEFAULT 1 COMMENT '주문 자동취소 일자 설정',
  `s_force_cashbill_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '현금영수증 필수 발행 사용여부',
  `s_force_cashbill_price` int(11) NOT NULL COMMENT '현금영수증 필수 발행 제한금액',
  `s_delivery_auto` int(4) NOT NULL COMMENT '자동 배송완료 처리 설정',
  `s_join_auth_kcb_enckey` varchar(255) NOT NULL COMMENT '본인인증 암호화키',
  `s_pg_paymethod_easypay` set('easypay_naverpay','easypay_kakaopay') DEFAULT NULL COMMENT '신용카드 간편결제 수단(easypay_kakaopay:카카오페이,easypay_naverpay:네이버페이)',
  `s_payple_use` enum('N','Y') NOT NULL DEFAULT 'N' COMMENT '간편결제(페이플) 사용여부',
  `s_payple_mode` enum('test','service') NOT NULL DEFAULT 'test' COMMENT '간편결제(페이플) 활성화 모드',
  `s_payple_cst_id` varchar(50) NOT NULL COMMENT '간편결제(페이플) 가맹점 ID(cst_id)',
  `s_payple_custKey` varchar(100) NOT NULL COMMENT '간편결제(페이플) 가맹점 운영 Key(custKey)',
  `s_payple_cancelKey` varchar(100) NOT NULL COMMENT '간편결제(페이플) 취소(환불) key',
  `s_category_display` int(11) NOT NULL COMMENT '베스트관리 PC진열설정',
  `s_category_display_mobile` int(11) NOT NULL COMMENT '베스트관리 모바일진열설정',
  `s_hosting_by` varchar(50) NOT NULL COMMENT '호스팅 사 입력창',
  `s_brand_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '브랜드 메뉴 노출여부(Y:노출 / N:비노출)',
  `s_display_content_open` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '상품상세 접기 기능 (Y:사용 / N: 미사용)',
  `member_login_time` int(11) NOT NULL DEFAULT 60 COMMENT '로그인 시도 횟수가 넘을 경우 중지될 시간초',
  `s_ticket_auto_C` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(카드)',
  `s_ticket_auto_L` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(실시간계좌이체)',
  `s_ticket_auto_B` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(무통장입금)',
  `s_ticket_auto_G` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(전액포인트)',
  `s_ticket_auto_V` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(가상계좌)',
  `s_ticket_auto_H` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(휴대폰)',
  `s_ticket_auto_P` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(페이코)',
  `s_ticket_auto_on` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '티켓상품 정산 자동처리 사용여부',
  `s_ticket_auto_PP` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품 페이플 간편결제 자동정산 처리 일수 추가',
  `s_main_best_title` varchar(100) NOT NULL DEFAULT '' COMMENT '메인 베스트 카테고리 타이틀',
  `s_main_best_limit` int(11) NOT NULL DEFAULT 20 COMMENT '메인 베스트 카테고리 노출 개수',
  `s_main_best_order` enum('date','recent','sales','random') NOT NULL DEFAULT 'date' COMMENT '메인 베스트 카테고리 정렬',
  `s_main_best_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '메인 베스트 카테고리 노출 여부',
  `apple_login_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '애플로그인 사용여부 :: (Y:사용, N:미사용)',
  `apple_clientid` varchar(255) DEFAULT NULL COMMENT '애플 클라이언트 아이디',
  `s_mobile_header_color` varchar(7) DEFAULT NULL COMMENT '모바일 브라우저 상단 색상',
  `s_promotion_plan_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '기획전 메뉴 노출여부',
  `s_promotion_plan_title` varchar(200) NOT NULL DEFAULT '' COMMENT '기획전 메뉴 명칭',
  `s_promotion_attend_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '출석체크 메뉴 노출여부',
  `s_request_partner_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '제휴문의 메뉴 노출여부',
  `s_main_best_display` int(11) NOT NULL DEFAULT 4 COMMENT '메인 베스트 카테고리 진열설정 (PC)',
  `s_main_best_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '메인 베스트 카테고리 진열설정 (모바일)',
  `s_brand_display` int(11) NOT NULL DEFAULT 4 COMMENT '브랜드 상품 진열설정 (PC)',
  `s_brand_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '브랜드 상품 진열설정 (모바일)',
  `s_promotion_display` int(11) NOT NULL DEFAULT 4 COMMENT '기획전 상품 진열설정 (PC)',
  `s_promotion_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '기획전 상품 진열설정 (모바일)',
  `s_relation_display` int(11) NOT NULL DEFAULT 4 COMMENT '상품상세 관련상품 진열설정 (PC)',
  `s_relation_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '상품상세 관련상품 진열설정(모바일)',
  `s_pg_apikey` varchar(255) DEFAULT NULL COMMENT '이니시스 API 취소키',
  `s_pg_escrow_apikey` varchar(255) DEFAULT NULL COMMENT '이니시스 에스크로 API 키',
  `s_main_timesale_title` varchar(100) NOT NULL DEFAULT '' COMMENT '메인 타임세일 타이틀',
  `s_main_timesale_limit` int(11) NOT NULL COMMENT '메인 타임세일 노출 개수',
  `s_main_timesale_view` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '메인 타임세일 관리 노출여부(Y:노출,N:비노출)',
  `s_main_timesale_display` int(11) NOT NULL DEFAULT 4 COMMENT '메인 타임세일 메인 진열설정(PC)',
  `s_main_timesale_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '메인 타임세일 메인 진열설정(모바일)',
  `s_main_timesale_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '메인 타임세일 메인진열설정 타입(box 박스형, list 리스트형)',
  `s_list_timesale_display` int(11) NOT NULL DEFAULT 4 COMMENT '메인 타임세일 목록 진열설정 (PC)',
  `s_list_timesale_mobile_display` int(11) NOT NULL DEFAULT 3 COMMENT '메인 타임세일 목록 진열설정 (모바일)',
  `s_list_timesale_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '메인 타임세일 목록 진열설정 타입 (box 박스형, list 리스트형)',
  `s_reply_writer` varchar(30) DEFAULT NULL COMMENT '답변용 작성자',
  `s_brand_limit` int(11) NOT NULL DEFAULT 20 COMMENT '브랜드 목록 상품 노출 개수 ',
  `s_saerch_text` varchar(255) DEFAULT NULL COMMENT '검색폼 문구설정',
  `s_main_best_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '메인 베스트 카테고리 진열설정 타입 (box 박스형,list 리스트형)',
  `s_search_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '상품목록 진열설정 타입 (box 박스형,list 리스트형)',
  `s_relation_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '상품상세 관련상품 진열설정 타입 (box 박스형,list 리스트형)',
  `s_brand_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '브랜드상품 진열설정 타입 (box 박스형,list 리스트형)',
  `s_promotion_display_type` enum('box','list') NOT NULL DEFAULT 'box' COMMENT '기획전 상품 진열설정 타입 (box 박스형,list 리스트형)',
  `s_main_timesale_order` enum('edate_asc','edate_desc','random') NOT NULL DEFAULT 'edate_asc' COMMENT '메인 타임세일 정렬 ( edate_asc:종료 빠른순,edate_desc:종료 느린순,random:랜덤)',
  `s_google_login_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '구글 로그인 사용여부',
  `s_google_client_id` varchar(255) NOT NULL COMMENT '구글 API 클라이언트 ID',
  `s_google_client_pw` varchar(255) NOT NULL COMMENT '구글 API 클라이언트 비밀번호',
  PRIMARY KEY (`s_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='사이트 설정';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_setup`
--

LOCK TABLES `smart_setup` WRITE;
/*!40000 ALTER TABLE `smart_setup` DISABLE KEYS */;
INSERT INTO `smart_setup` VALUES (1,'master','*4999DBFBDAA3384212A7934E8106780D2386C983','0000-0000','010-0000-0000','sms_id','KCP0W7NWmrZYv6F71nJyjP7ZICDGZHyIkfCJxEzClUJLDwovWqIsSneEwwqzsQR38rHZEG0HxVOIfknn32%C2%A7jAQ%3D%3D','help@domain.com','사이트','사이트 타이틀','원데이넷  반응형 쇼핑몰 솔루션','원데이넷, 쇼핑몰솔루션,쇼핑몰, 솔루션, 상상너머, 반응형, 배송상품, 티켓, 타임세일,모바일관리자','','월~금 : 09:30~17:30',1000,10000,1000,7,2,500,0,'inicis','--test_id','--test_id','test_sk_zXLkKEypNArWmo50nX3lmeaxYG5R','CSN71931','test',3,3000,50000,'CJ대한통운택배','주중 2-3일','','서울 특별시 강남구 물류창고','','','홍길동','회사','0000-0000','0000-0000','서울 특별시 강남구 역삼동','000-0000-0000','홍길동','Y','Y','','','N','N','test','','Y',4,2,'3513017475.gif','Y',4,2,'4058552318.gif','Y',4,2,'3639150490.gif','thumb',4,3,'N','S6186','ONEDAYNET20240228-HYPLUS295382',5,'persent',NULL,'responsive-food','responsive-food','0000-0000','help@domain','Y','8c432619cb96832ce764259dec247ba2','ebf41bed88c77dcb3d79fd2aeccfcf0a','','','N','N','N','test','','','','','test','','',1200,1,0,2,'Y','Y',3,3,2,3,3,3,0,'Y',3,'서비스','솔루션개발','test','8FE6E4DB-E408-4899-8A5E-57DB781245FA','N','hyssenceplus','주식회사 상상너머','hyssenceplus','Y','N','Y','N','N','N','Y','Y','N','admin',4,10,4,0,'Y',2,'Y',2,'naver.com,gmail.com,nate.com,daum.net,kakao.com','N','2023-03-30','본메일은 발신 전용 메일입니다. 메일수신을 원치 않으시면 [__deny__]를 눌러주십시오.\r\nif you do not want this of email_information, please click the [__deny__]','080-0000-0000','Y','N','','','--test_skey','--test_skey','Y','Y','Y','Y','Y','C','C','Y','Y','KRW','us','Y','KRW','cn','Y','KRW','jp','N','만료','0000-00-00','0000-00-00','','443','N','_config.ssl.form.php§_point.list.php','N','service.main§shop.cart.list§product.list','N','service.main§shop.cart.list§product.list','A','','','','N','<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">회원 가입 안내</p><br>\r\n				<p>① 저희 쇼핑몰은 기본적으로 회원제로 운영하고 있습니다. 운영방침에 따라 비회원구매가 가능한 경우가 있습니다. <br>\r\n				② 회원 가입은 월회비, 연회비등 어떠한 비용도 청구하지 않으며 100% 무료로 진행됩니다. <br>\r\n				③ 회원 가입 시 가입 환영 축하금으로 <b>[적림급 설정::회원가입 지급금액]</b><strong>원</strong>의 적립금이 지급됩니다.&nbsp;</p><p>\r\n				④ 구매 시 적립된 적립금은 <strong>[적림급 설정::사용 최소금액]원</strong>부터 현금처럼 사용할 수 있습니다. <br>\r\n				⑤ 적립금 제도는 온라인으로 가입한 회원에게만 적용되는 멤버십 혜택입니다. </p><br>\r\n				<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">적립금 제도</p><br>\r\n				<p>① 저희 쇼핑몰은 상품에 따라 일정 비율로 적립금이 자동으로 적립됩니다.<br>\r\n				② 적립금 100원은 현금 100원과 같습니다. <br>\r\n				③ 적립금은 <b>[적림급 설정::사용 최소금액]</b><strong>원</strong> 이상 되면 사용할 수 있고 한 주문 당 <b>[적림급 설정::사용한도]</b><strong>원</strong> 이 넘는 금액의 적립금은 사용할 수 없습니다.</p><br>\r\n				<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">상품 주문 방법</p><br>\r\n				<p>저희 쇼핑몰에서 상품을 주문하는 방법은 크게 7단계입니다. <br>\r\n				① 상품검색<br>\r\n				② 바로구매 혹은 장바구니에 담기<br>\r\n				③ 회원으로 로그인 후 주문 (정책에 따라 비회원 구매 가능)<br>\r\n				④ 주문서 작성 <br>\r\n				⑤ 결제방법 선택 및 결제 <br>\r\n				⑥ 주문 완료 (주문번호) <br>\r\n				⑦ 마이페이지 주문내역 확인 (배송확인)</p><br>\r\n				<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">주문확인 및 배송조회</p><br>\r\n				<p>쇼핑몰에서 주문을 하셨을 경우 마이페이지의 주문내역에서 주문상황을 바로 확인 하실 수 있습니다. <br>\r\n				주문단계에 따라 주문취소/환불 등이 가능한 경우가 있고 불가능한 경우가 있으니 이에 대한 안내는 1:1온라인문의나 고객센터<!--치환자-->([쇼핑몰 기본정보::고객센터 대표번호])를 이용해주세요.<br>\r\n				정책에 따라 비회원 구매가 가능한 경우에는 주문번호를 꼭 기억하고 계셔야 확인이 가능합니다.<br>\r\n				현재 배송은 <b>[상품/배송::택배사]</b>&nbsp;택배 서비스를 이용하고 있습니다.&nbsp;</p><p>\r\n				주문상태가 배송으로 바뀌면 택배 운송장번호로 택배사 홈페이지 배송추적을 통해 정확한 배송상태를 추적하실 수 있습니다.</p><br>\r\n				<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">안전한 대금 결제 시스템</p><br>\r\n				<p>\r\n				저희 쇼핑몰은 안전한 전자 결제 서비스(PG)에 가입이 되어있으며, 가능한 결제 수단은 주문결제 단계에서 확인할 수 있습니다.<br>\r\n				무통장 입금은 PC뱅킹, 인터넷뱅킹, 텔레뱅킹 혹은 가까운 은행에서 직접 입금하시면 되고, 신용카드 결제는 [통합 전자결제(PG) 관리::PG사] 전자결제 서비스를 이용하므로 보안문제는 걱정하지 않으셔도 되며, 고객님의 이용내역서에는 [통합 전자결제(PG) 관리::PG사]에 기록됩니다. <br><br>\r\n\r\n				이용 가능한 국내 발행 신용카드 <br>\r\n				- 국내발행 모든 신용카드 <br><br>\r\n\r\n				이용 가능한 해외 발생 신용카드 <br>\r\n				- VISA Card, MASTER Card, AMEX Card <br><br>\r\n\r\n				무통장 입금 가능 은행 <br>\r\n				- 주문시 무통장 입금을 선택할 경우 가능한 은행 목록을 확인 하실 수 있습니다.<br>\r\n				- 무통장 입금시의 송금자 이름은 주문시 입력하신 주문자의 실명이어야 합니다. </p><br>\r\n				<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">배송기간 및 배송방법</p><br>\r\n				<p>무통장 입금의 경우는 입금하신 날로부터, 신용카드로 구매하신 경우에는 구매하신 날로부터 2-3일 이내에(최장 7일이내) 입력하신 배송처로 주문상품이 도착하게 됩니다. <br>\r\n				주문하신 상품에 따라 배송기간이 조금 상이할 수 있으니 자세한 사항은 상품상세페이지에 명시되어있는 배송관련 내용을 참조해주세요.<br>\r\n				현재 배송은 <b>[상품/배송::택배사]</b>&nbsp;택배 서비스를 이용하고 있습니다.</p><br>\r\n				<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">주문취소, 교환 및 환불</p><br>\r\n				<p>\r\n				쇼핑몰은 소비자의보호를 위해서 규정한 제반 법규를 준수합니다. <br>\r\n				주문 취소는 미결재인 상태에서는 고객님이 직접 취소하실 수가 있습니다. 결제후 취소는 저희 고객센터<!--치환자-->([쇼핑몰 기본정보::고객센터 대표번호])로 문의해 주시기 바랍니다. <br>\r\n				무통장 입금의 경우 일정기간동안 송금을 하지 않으면 자동 주문 취소가 되고, 구매자가 원하는 경우 인터넷에서 바로 취소 하실 수도 있으며, 송금을 하신 경우에는 환불조치 해드립니다. <br>\r\n				카드로 결제하신 경우, 승인 취소가 가능하면 취소을 해드리지만 승인 취소가 불가능한 경우 해당 금액을 모두 송금해 드립니다. <br>\r\n				이때 승인취소는 카드사에 따라 2-3일 정도 소요될 수 있습니다.<br>\r\n				반송을 하실 때에는 주문번호, 회원정보를 메모하여 보내주시면 보다 신속한 처리에 도움이 됩니다.<br>\r\n				상품에 문제가 있거나 교환/환불을 원하는 경우 고객센터로 먼저 연락해주시고, 반송주소로 주문번호, 회원정보(이름,휴대폰 번호 등), 반품사유 등을 메모하여 반송해 주시면 상품 대금은 예치되거나 고객센터를 통해 환불조치 됩니다. (반송료는 고객 부담 입니다.)<br><br>\r\n\r\n				반송주소 : 하단 카피라잇부분의 사업장소재지(주소)를 참고해 주십시오.</p>','<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">회원 가입 안내</p><br>\r\n			<p>① 저희 쇼핑몰은 기본적으로 회원제로 운영하고 있습니다. 운영방침에 따라 비회원구매가 가능한 경우가 있습니다. <br>\r\n			② 회원 가입은 월회비, 연회비등 어떠한 비용도 청구하지 않으며 100% 무료로 진행됩니다. <br>\r\n			③ 회원 가입 시 가입 환영 축하금으로 <b>[적림급 설정::회원가입 지급금액]</b><strong>원</strong>의 적립금이 지급됩니다.&nbsp;</p><p>\r\n			④ 구매 시 적립된 적립금은 <b>[적림급 설정::사용 최소금액]</b><strong>원</strong>부터 현금처럼 사용할 수 있습니다.&nbsp;</p><p>\r\n			⑤ 적립금 제도는 온라인으로 가입한 회원에게만 적용되는 멤버쉽 혜택입니다. </p><br>\r\n			<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">적립금 제도</p><br>\r\n			<p>① 저희 쇼핑몰은 상품에 따라 일정 비율로 적립금이 자동으로 적립됩니다.<br>\r\n			② 적립금 100원은 현금 100원과 같습니다. <br>\r\n			③ 적립금은 <b>[적림급 설정::사용 최소금액]원</b> 이상 되면 사용할 수 있고 한 주문 당 <b>[적림급 설정::사용한도]원</b> 이 넘는 금액의 적립금은 사용할 수 없습니다. </p><br>\r\n			<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">상품 주문 방법</p><br>\r\n			<p>저희 쇼핑몰에서 상품을 주문하는 방법은 크게 7단계입니다. <br>\r\n			① 상품검색<br>\r\n			② 바로구매 혹은 장바구니에 담기<br>\r\n			③ 회원으로 로그인 후 주문 (정책에 따라 비회원 구매 가능)<br>\r\n			④ 주문서 작성 <br>\r\n			⑤ 결제방법 선택 및 결제 <br>\r\n			⑥ 주문 완료 (주문번호) <br>\r\n			⑦ 마이페이지 주문내역 확인 (배송확인)</p><br>\r\n			<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">주문확인 및 배송조회</p><br>\r\n			<p>쇼핑몰에서 주문을 하셨을 경우 마이페이지의 주문내역에서 주문상황을 바로 확인 하실 수 있습니다. <br>\r\n			주문단계에 따라 주문취소/환불 등이 가능한 경우가 있고 불가능한 경우가 있으니 이에 대한 안내는 1:1온라인문의나 고객센터<!--치환자-->([쇼핑몰 기본정보::고객센터 대표번호])를 이용해주세요.<br>\r\n			정책에 따라 비회원 구매가 가능한 경우에는 주문번호를 꼭 기억하고 계셔야 확인이 가능합니다.<br>\r\n			현재 배송은 <b>[상품/배송::택배사]</b>&nbsp;택배 서비스를 이용하고 있습니다. <br>\r\n			주문상태가 배송으로 바뀌면 택배 운송장번호로 택배사 홈페이지 배송추적을 통해 정확한 배송상태를 추적하실 수 있습니다.</p><br>\r\n			<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">안전한 대금 결제 시스템</p><br>\r\n			<p>\r\n			저희 쇼핑몰은 안전한 전자 결제 서비스(PG)에 가입이 되어있으며, 가능한 결제 수단은 주문결제 단계에서 확인할 수 있습니다.<br>\r\n			무통장 입금은 PC뱅킹, 인터넷뱅킹, 텔레뱅킹 혹은 가까운 은행에서 직접 입금하시면 되고, 신용카드 결제는 [통합 전자결제(PG) 관리::PG사] 전자결제 서비스를 이용하므로 보안문제는 걱정하지 않으셔도 되며, 고객님의 이용내역서에는 [통합 전자결제(PG) 관리::PG사]에 기록됩니다. <br><br>\r\n\r\n			이용 가능한 국내 발행 신용카드 <br>\r\n			- 국내발행 모든 신용카드 <br><br>\r\n\r\n			이용 가능한 해외 발생 신용카드 <br>\r\n			- VISA Card, MASTER Card, AMEX Card <br><br>\r\n\r\n			무통장 입금 가능 은행 <br>\r\n			- 주문시 무통장 입금을 선택할 경우 가능한 은행 목록을 확인 하실 수 있습니다.<br>\r\n			- 무통장 입금시의 송금자 이름은 주문시 입력하신 주문자의 실명이어야 합니다. </p><br>\r\n			<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">배송기간 및 배송방법</p><br>\r\n			<p>무통장 입금의 경우는 입금하신 날로부터, 신용카드로 구매하신 경우에는 구매하신 날로부터 2-3일 이내에(최장 7일이내) 입력하신 배송처로 주문상품이 도착하게 됩니다. <br>\r\n			주문하신 상품에 따라 배송기간이 조금 상이할 수 있으니 자세한 사항은 상품상세페이지에 명시되어있는 배송관련 내용을 참조해주세요.<br>\r\n			현재 배송은 <b>[상품/배송::택배사]</b>&nbsp;택배 서비스를 이용하고 있습니다. </p><br>\r\n			<p style=\"font-size:15px; color:#000; letter-spacing:-0.5px; font-weight:600;\">주문취소, 교환 및 환불</p><br>\r\n			<p>\r\n			쇼핑몰은 소비자의보호를 위해서 규정한 제반 법규를 준수합니다. <br>\r\n			주문 취소는 미결재인 상태에서는 고객님이 직접 취소하실 수가 있습니다. 결제후 취소는 저희 고객센터<!--치환자-->([쇼핑몰 기본정보::고객센터 대표번호])로 문의해 주시기 바랍니다. <br>\r\n			무통장 입금의 경우 일정기간동안 송금을 하지 않으면 자동 주문 취소가 되고, 구매자가 원하는 경우 인터넷에서 바로 취소 하실 수도 있으며, 송금을 하신 경우에는 환불조치 해드립니다. <br>\r\n			카드로 결제하신 경우, 승인 취소가 가능하면 취소을 해드리지만 승인 취소가 불가능한 경우 해당 금액을 모두 송금해 드립니다. <br>\r\n			이때 승인취소는 카드사에 따라 2-3일 정도 소요될 수 있습니다.<br>\r\n			반송을 하실 때에는 주문번호, 회원정보를 메모하여 보내주시면 보다 신속한 처리에 도움이 됩니다.<br>\r\n			상품에 문제가 있거나 교환/환불을 원하는 경우 고객센터로 먼저 연락해주시고, 반송주소로 주문번호, 회원정보(이름,휴대폰 번호 등), 반품사유 등을 메모하여 반송해 주시면 상품 대금은 예치되거나 고객센터를 통해 환불조치 됩니다. (반송료는 고객 부담 입니다.)<br><br>\r\n\r\n			반송주소 : 하단 카피라잇부분의 사업장소재지(주소)를 참고해 주십시오.</p>','','Y','test','test','N','','M','2506535107.png','3806191906.png','Y','Y','2024-02-27','1708995234','Y','Y','Y','Y','Y','등급',24,0,'Y','test','Y','Y','Y','Y',60,'a:7:{s:3:\"use\";s:1:\"N\";s:5:\"sdate\";s:10:\"2017-11-22\";s:5:\"edate\";s:10:\"2023-01-01\";s:8:\"minPrice\";s:0:\"\";s:9:\"setMember\";s:3:\"all\";s:11:\"setGroupUid\";s:0:\"\";s:5:\"mdate\";s:19:\"2022-12-30 00:14:59\";}','https://instagram.com','https://facebook.com','https://twitter.com','https://blog.naver.com','','https://youtube.com','https://pf.kakao.com/id/chat','','쿠키,생일파티,유기농디저트,디저트,기념일,유기농쿠키,케이크케이크,케이크,가을,원데이넷,하이센스,반응형,상상너머','패밀리케이크,마카롱,미니츄러스,티켓','Y','2024-02-27 09:53:54','month','monthlast','Y','auth','','point|coupon|brand|maker/orgin|screenPrice','','screenPrice|point|coupon|indrbenefits','brand|maker|orgin|deliveryInfo|subname|optionStock','',0,0,'Y',0,0,'Y','R',3,'A',30,'category,brand,hashtag,price,boon',5,3,'review',20,'rand','a:3:{s:6:\"writer\";s:19:\"관리자,운영자\";s:5:\"title\";s:13:\"홍보,광고\";s:7:\"content\";s:6:\"도박\";}','a:1:{s:4:\"type\";s:83:\"이용안내,회원관련,주문/결제/배송,교환/환불/반품,적립금관련\";}','회원,로그인,상품,주문,배송,주문배송,환불/기타,회원가입','N','Array','Y','2,3,4,5,6,7,8','','D','N','Y','auto','N','test','','','','','01,02,04,31,35,98,75,77','N','pg','Y','all','Y','','','','Y','Y','Y','Y','','','2023-02-17 14:37:01','0000-00-00 00:00:00','N','D','3646972799.png','N','N',1,'Y',100000,0,'','','Y','test','test','abcd1234567890','a41ce010ede9fcbfb3be86b24858806596a9db68b79d138b147c3e563e1829a0',5,2,'(주)상상너머','Y','N',0,3,3,3,3,3,3,0,'N',3,'BEST',20,'date','Y','N','','#111111','Y','기획전','Y','Y',4,2,5,2,3,2,5,3,'--test_api','--test_api','Time Sale',50,'Y',3,2,'box',5,1,'box','관리자',20,NULL,'box','box','box','box','box','edate_asc','N','','');
/*!40000 ALTER TABLE `smart_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_site_title`
--

DROP TABLE IF EXISTS `smart_site_title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_site_title` (
  `sst_uid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `sst_name` varchar(30) NOT NULL DEFAULT '' COMMENT '페이지명',
  `sst_page` varchar(255) NOT NULL DEFAULT '' COMMENT '페이지URL',
  `sst_title` varchar(255) NOT NULL DEFAULT '' COMMENT '페이지 타이틀',
  `sst_desc` text CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL COMMENT '페이지 Desc',
  PRIMARY KEY (`sst_uid`),
  KEY `sst_name` (`sst_name`)
) ENGINE=MyISAM AUTO_INCREMENT=260 DEFAULT CHARSET=utf8 COMMENT='사이트 타이틀 설정';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_site_title`
--

LOCK TABLES `smart_site_title` WRITE;
/*!40000 ALTER TABLE `smart_site_title` DISABLE KEYS */;
INSERT INTO `smart_site_title` VALUES (216,'기본페이지 - 메인페이지','§§/§§','{공통타이틀}',''),(217,'기본페이지 - 상품목록','§§/?pn=product.list§§','{카테고리명} - {사이트명}',''),(218,'기본페이지 - 쇼핑몰 기획전 홈','§§/?pn=product.promotion_list§§','기획전 - {사이트명}',''),(219,'기본페이지 - 쇼핑몰 기획전 상세','§§/?pn=product.promotion_view§§','{기획전명} - {사이트명}',''),(220,'기본페이지 - 브랜드상품','§§/?pn=product.brand_list§§','브랜드 상품 - {사이트명}',''),(221,'기본페이지 - 브랜드상품(브랜드 선택 시)','§§/?pn=product.brand_list&uid=§§','{브랜드명} - {사이트명}',''),(222,'기본페이지 - 상품상세보기','§§/?pn=product.view§§','{상품명} - {사이트명}',''),(223,'기본페이지 - 상품검색','§§/?pn=product.search.list§§','{검색어} 검색결과 - {사이트명}',''),(224,'기본페이지 - 장바구니','§§/?pn=shop.cart.list§§','장바구니 - {사이트명}',''),(225,'기본페이지 - 주문/결제','§§/?pn=shop.order.form§§/?pn=shop.order.result§§','주문/결제 - {사이트명}',''),(226,'기본페이지 - 주문완료','§§/?pn=shop.order.complete§§','주문완료 - {사이트명}',''),(227,'멤버십 - 로그인','§§/?pn=member.login.form§§','로그인 - {사이트명}',''),(228,'멤버십 - 회원가입','§§/?pn=member.join.agree§§/?pn=member.join.form§§','회원가입 - {사이트명}',''),(229,'멤버십 - 가입완료','§§/?pn=member.join.complete§§','가입완료 - {사이트명}',''),(230,'멤버십 - 아이디/비밀번호 찾기','§§/?pn=member.find.form§§','아이디/비밀번호 찾기 - {사이트명}',''),(231,'마이페이지 - 메인','§§/?pn=mypage.main§§','마이페이지 - {사이트명}',''),(232,'마이페이지 - 주문내역','§§/?pn=mypage.order.list§§/?pn=mypage.order.view§§','주문내역 - {사이트명}',''),(233,'마이페이지 - 적립금','§§/?pn=mypage.point.list§§','적립금 - {사이트명}',''),(234,'마이페이지 - 쿠폰','§§/?pn=mypage.coupon.list§§','쿠폰 - {사이트명}',''),(235,'마이페이지 - 1:1 온라인 문의','§§/?pn=mypage.inquiry.form§§','1:1 온라인 문의 - {사이트명}',''),(236,'마이페이지 - 찜한상품','§§/?pn=mypage.wish.list§§','찜한상품 - {사이트명}',''),(237,'마이페이지 - 문의내역','§§/?pn=mypage.inquiry.list§§','문의내역 - {사이트명}',''),(238,'마이페이지 - 상품후기','§§/?pn=mypage.eval.list§§','상품후기 - {사이트명}',''),(239,'마이페이지 - 상품문의','§§/?pn=mypage.qna.list§§','상품문의 - {사이트명}',''),(240,'마이페이지 - 정보수정','§§/?pn=mypage.modify.form§§','정보수정 - {사이트명}',''),(241,'마이페이지 - 로그인기록','§§/?pn=mypage.login.log§§','로그인기록 - {사이트명}',''),(242,'마이페이지 - 회원탈퇴','§§/?pn=mypage.leave.form§§','회원탈퇴 - {사이트명}',''),(243,'게시판 - 게시판 리스트','§§/?pn=board.list§§','{게시판명} - {사이트명}',''),(244,'게시판 - 게시판 상세보기','§§/?pn=board.view§§','{게시물제목} - {사이트명}',''),(245,'게시판 - 게시판 글쓰기','§§/?pn=board.form§§','{게시판명} - {사이트명}',''),(246,'고객센터 - 메인','§§/?pn=service.main§§','고객센터 - {사이트명}',''),(247,'고객센터 - 자주 묻는 질문','§§/?pn=faq.list§§','자주 묻는 질문 - {사이트명}',''),(248,'고객센터 - 미확인 입금자','§§/?pn=service.deposit.list§§','미확인 입금자 - {사이트명}',''),(249,'커뮤니티 - 상품후기','§§/?pn=service.eval.list§§','상품후기 - {사이트명}',''),(250,'커뮤니티 - 상품문의','§§/?pn=service.qna.list§§','상품문의 - {사이트명}',''),(251,'커뮤니티 - 제휴문의','§§/?pn=service.partner.form§§','제휴문의 - {사이트명}',''),(252,'커뮤니티 - 출석체크','§§/?pn=promotion.attend§§','출석체크 - {사이트명}',''),(253,'일반페이지 - 회사소개','§§/?pn=pages.view&type=agree&data=company§§','회사소개 - {사이트명}',''),(254,'일반페이지 - 이용안내','§§/?pn=pages.view&type=agree&data=guide§§','이용안내 - {사이트명}',''),(255,'일반페이지 - 이용약관','§§/?pn=pages.view&type=agree&data=agree§§','이용약관 - {사이트명}',''),(256,'일반페이지 - 개인정보처리방침','§§/?pn=pages.view&type=agree&data=privacy§§','개인정보처리방침 - {사이트명}',''),(257,'일반페이지 - 이메일무단수집거부','§§/?pn=pages.view&type=agree&data=deny§§','이메일무단수집거부 - {사이트명}',''),(258,'adsasd','§§/config.title.form.php?menuUid=252§§','{사이트명}',''),(259,'asd','§§asdasd§§','{사이트명}','');
/*!40000 ALTER TABLE `smart_site_title` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_sms_log`
--

DROP TABLE IF EXISTS `smart_sms_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_sms_log` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유키',
  `code` varchar(5) NOT NULL COMMENT '에러코드',
  `msg` varchar(255) NOT NULL COMMENT '에러메시지',
  `send_num` varchar(20) NOT NULL COMMENT '보내는 번호',
  `receive_num` varchar(20) NOT NULL COMMENT '받는번호',
  `rdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '기록일',
  PRIMARY KEY (`idx`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='SMS 발송 에러로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_sms_log`
--

LOCK TABLES `smart_sms_log` WRITE;
/*!40000 ALTER TABLE `smart_sms_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_sms_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_sms_set`
--

DROP TABLE IF EXISTS `smart_sms_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_sms_set` (
  `ss_uid` varchar(30) NOT NULL COMMENT '문자발송 유형',
  `ss_status` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '발송여부상태',
  `ss_text` text NOT NULL COMMENT '문자내용',
  `ss_title` varchar(100) NOT NULL COMMENT '제목',
  `ss_file` varchar(100) NOT NULL COMMENT '첨부파일',
  `kakao_status` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '알림톡 발송여부',
  `kakao_templet_num` varchar(50) NOT NULL COMMENT '알림톡 - 템플릿 번호',
  `kakao_add1` varchar(50) NOT NULL COMMENT '알림톡 - 치환용 추가정보 - 치환자 입력',
  `kakao_add2` varchar(50) NOT NULL COMMENT '알림톡 - 치환용 추가정보 - 치환자 입력',
  `kakao_add3` varchar(50) NOT NULL COMMENT '알림톡 - 치환용 추가정보 - 치환자 입력',
  `kakao_add4` varchar(50) NOT NULL COMMENT '알림톡 - 치환용 추가정보 - 치환자 입력',
  `kakao_add5` varchar(50) NOT NULL COMMENT '알림톡 - 치환용 추가정보 - 치환자 입력',
  `kakao_add6` varchar(50) NOT NULL COMMENT '알림톡 - 치환용 추가정보 - 치환자 입력',
  `kakao_add7` varchar(50) NOT NULL COMMENT '알림톡 - 치환용 추가정보 - 치환자 입력',
  `kakao_add8` varchar(50) NOT NULL COMMENT '알림톡 - 치환용 추가정보 - 치환자 입력',
  PRIMARY KEY (`ss_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='SMS 발송문구 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_sms_set`
--

LOCK TABLES `smart_sms_set` WRITE;
/*!40000 ALTER TABLE `smart_sms_set` DISABLE KEYS */;
INSERT INTO `smart_sms_set` VALUES ('join','Y','[{사이트명}] 회원가입을 환영합니다. 즐거운 쇼핑이 되도록 노력하겠습니다.','','','N','','','','','','','','',''),('order_online','N','[{사이트명}] 고객님 주문이 완료되었습니다. 입금이 확인되면 상품이 배송됩니다. 감사합니다. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('order_pay','N','[{사이트명}] 고객님의 주문이 완료되었습니다. [주문번호: {주문번호}] 주중 배송기준에 따라 상품이 배송됩니다. 감사합니다.','','','N','','','','','','','','',''),('online_pay','N','[{사이트명}] 고객님의 입금이 확인되었습니다. [주문번호: {주문번호}] 주중 배송기준에 따라 상품이 배송됩니다. 감사합니다.','','','N','','','','','','','','',''),('delivery','N','[{사이트명}] 고객님이 주문하신 상품이 발송되었습니다. 마이페이지에서 배송상황을 확인하실 수 있습니다. 감사합니다. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('order_cancel','N','[{사이트명}] 주문이 정상적으로 취소되었습니다. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('request','Y','[{사이트명}] 고객님 온라인문의가 접수되었습니다. 빠른 시간내에 문의에 대한 답변을 드리겠습니다. 감사합니다.','','','N','','','','','','','','',''),('admin_join','N','[{사이트명}] {회원명}님이 회원가입을 하였습니다.','','','N','','','','','','','','',''),('admin_order_online','N','[{사이트명}] 무통장주문이 확인되었습니다. 관리자페이지에서 확인해주세요. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('admin_order_pay','N','[{사이트명}] 주문결제가 완료되었습니다. 관리자페이지에서 확인해 주십시오. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('admin_online_pay','N','[{사이트명}] 주문의 입금이 확인되었습니다. 관리자페이지에서 확인해 주십시오. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('admin_delivery','N','[{사이트명}] 상품이 발송되었습니다. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('admin_order_cancel','N','[{사이트명}] 주문이 정상적으로 취소되었습니다. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('admin_request','Y','[{사이트명}] {회원명} 님께서 온라인문의를 접수하셨습니다. 관리자페이지에서 답변을 등록해 주십시오.','','','N','','','','','','','','',''),('order_virtual','N','[{사이트명}] 고객님 주문이 완료되었습니다. 입금이 확인되면 상품이 배송됩니다. 감사합니다. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('admin_order_virtual','N','[{사이트명}] 가상계좌주문이 확인되었습니다. 관리자페이지에서 확인해주세요. [주문번호: {주문번호}]','','','N','','','','','','','','',''),('2year_opt','Y','[{사이트명}] {회원명}님. \r\n수신동의 후 2년이 경과하였습니다.\r\n\r\n정보통신망법 제50조제8항 및 동법 시행령 제62조의3은 최초 동의한 날로부터 매2년마다 하도록 규정하고 있습니다. \r\n\r\n이에 따라 수신동의 받은 날부터 매 2년 마다 수신동의 여부를 재확인 해야 합니다.\r\n\r\n사이트에 접속하시어 로그인 하신 후 마이페이지 > 정보수정을 통해 이메일 및 SMS에 대한 수신여부를 확인해주시기 바랍니다.\r\n\r\n본 문자는 수신동의하신지 2년이 지난 회원중 SMS 수신에 동의 하신 회원에게만 발송이 됩니다.\r\n\r\n감사합니다.','','','N','','','','','','','','',''),('order_cancel_part','N','[{사이트명}] 주문하신 상품 중 일부 상품의 주문이 취소되었습니다. [주문번호: {주문번호}] [취소된상품: {주문상품명}]','','','N','','','','','','','','',''),('admin_order_cancel_part','N','[{사이트명}] 주문하신 상품 중 일부 상품의 주문이 취소되었습니다. [주문번호: {주문번호}] [취소된상품: {주문상품명}]','','','N','','','','','','','','',''),('temp_password','Y','[{사이트명}] {회원명}님 임시 비밀번호를 안내드립니다. 임시 비밀번호: {임시비밀번호}','','','N','','','','','','','','',''),('admin_temp_password','N','[{사이트명}] {회원명}님께서 임시비밀번호를 발급받으셨습니다.','','','N','','','','','','','','',''),('admin_2year_opt','N','','','','N','','','','','','','','',''),('product_review','N','[{사이트명}] 고객님 상품후기가 접수되었습니다. [상품: {후기(문의)상품명}]','','','N','','','','','','','','',''),('admin_product_review','Y','[{사이트명}] {회원명} 님께서 상품후기를 등록하셨습니다. [상품: {후기(문의)상품명}]','','','N','','','','','','','','',''),('product_talk','N','[{사이트명}] 고객님 상품문의가 접수되었습니다. 빠른 시간내에 문의에 대한 답변을 드리겠습니다. 감사합니다. [상품: {후기(문의)상품명}]','','','N','','','','','','','','',''),('admin_product_talk','Y','[{사이트명}] {회원명} 님께서 상품문의를 접수하셨습니다. 관리자페이지에서 답변을 등록해 주십시오. [상품: {후기(문의)상품명}]','','','N','','','','','','','','',''),('cancel_part_request','N','[{사이트명}] 주문하신 상품 중 일부 상품의 주문취소요청이 정상적으로 접수 되었습니다. [주문번호: {주문번호}] [취소요청된상품: {주문상품명}]','','','N','','','','','','','','',''),('admin_cancel_part_request','N','[{사이트명}] 주문하신 상품 중 일부 상품의 주문취소요청이 접수되었습니다. [주문번호: {주문번호}] [취소요청된상품: {주문상품명}]','','','N','','','','','','','','',''),('ticket','N','[{회원명}] 고객님 주문하신 티켓이 발급되었습니다.','','','N','','','','','','','','',''),('ticket_use','N','{주문자명}님 티켓이 사용완료되었습니다. 티켓번호: {티켓번호}','','','N','','','','','','','','',''),('ticket_unuse','N','{주문자명}님 티켓이 사용취소되었습니다. 티켓번호: {티켓번호}','','','N','','','','','','','','',''),('admin_ticket','N','[{회원명}] 고객님의  주문  티켓이 발급되었습니다.','','','N','','','','','','','','',''),('admin_ticket_use','N','{주문자명}님의 티켓이 사용완료되었습니다. 티켓번호: {티켓번호}','','','N','','','','','','','','',''),('admin_ticket_unuse','N','{주문자명}님의 티켓이 사용취소되었습니다. 티켓번호: {티켓번호}','','','N','','','','','','','','','');
/*!40000 ALTER TABLE `smart_sms_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_sns_log`
--

DROP TABLE IF EXISTS `smart_sns_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_sns_log` (
  `sl_uid` int(11) NOT NULL AUTO_INCREMENT,
  `sl_pcode` varchar(20) NOT NULL COMMENT '상품코드',
  `sl_type` varchar(30) NOT NULL DEFAULT '' COMMENT 'sns 유형',
  `sl_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '유저 ip',
  `sl_rdate` datetime NOT NULL COMMENT '발송일시',
  PRIMARY KEY (`sl_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='SNS 이용 로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_sns_log`
--

LOCK TABLES `smart_sns_log` WRITE;
/*!40000 ALTER TABLE `smart_sns_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_sns_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_stack_never`
--

DROP TABLE IF EXISTS `smart_stack_never`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_stack_never` (
  `sn_uid` int(11) NOT NULL AUTO_INCREMENT,
  `sn_unique_type` varchar(50) NOT NULL COMMENT '유니크 타입 (타입)',
  `sn_unique_value` varchar(100) NOT NULL COMMENT '유니크 값 (100글자제한)',
  `sn_cnt` int(11) NOT NULL COMMENT '유니크 실행횟수',
  `sn_update_dt` datetime NOT NULL COMMENT '업데이트 날짜',
  `sn_reg_dt` datetime NOT NULL COMMENT '등록 날짜',
  PRIMARY KEY (`sn_uid`),
  UNIQUE KEY `sn_unique` (`sn_unique_type`,`sn_unique_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='스택니버 테이블(유니크 데이터 저장소)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_stack_never`
--

LOCK TABLES `smart_stack_never` WRITE;
/*!40000 ALTER TABLE `smart_stack_never` DISABLE KEYS */;
/*!40000 ALTER TABLE `smart_stack_never` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_table_text`
--

DROP TABLE IF EXISTS `smart_table_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_table_text` (
  `ttt_uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ttt_tablename` varchar(50) NOT NULL DEFAULT '' COMMENT '대상 테이블명',
  `ttt_datauid` varchar(50) NOT NULL DEFAULT '0' COMMENT '대상 데이터 고유번호',
  `ttt_keyword` varchar(50) NOT NULL DEFAULT '' COMMENT '추가할 항목 - key(상세요강,자기소개서, 경력사항 등)',
  `ttt_value` text DEFAULT NULL COMMENT '추가할 항목명',
  PRIMARY KEY (`ttt_uid`),
  KEY `ttt_tablename` (`ttt_tablename`),
  KEY `ttt_datauid` (`ttt_datauid`),
  KEY `ttt_keyword` (`ttt_keyword`),
  KEY `ttt_package` (`ttt_tablename`,`ttt_keyword`,`ttt_datauid`)
) ENGINE=MyISAM AUTO_INCREMENT=896 DEFAULT CHARSET=utf8 COMMENT='테이블별 텍스트 항목 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_table_text`
--

LOCK TABLES `smart_table_text` WRITE;
/*!40000 ALTER TABLE `smart_table_text` DISABLE KEYS */;
INSERT INTO `smart_table_text` VALUES (1,'smart_order_settle_complete','3','s_opuid','159'),(2,'smart_order_settle_complete','4','s_opuid','60,61'),(3,'smart_order_settle_complete','5','s_opuid','1,2,3,4'),(4,'smart_order_settle_complete','6','s_opuid','5,6,7,8'),(5,'smart_order_settle_complete','7','s_opuid','9'),(6,'smart_order_settle_complete','8','s_opuid','9'),(7,'smart_order_settle_complete','9','s_opuid','14'),(8,'smart_order_settle_complete','10','s_opuid','15'),(9,'smart_order_settle_complete','11','s_opuid','16'),(10,'smart_order_settle_complete','12','s_opuid','16'),(11,'smart_order_settle_complete','13','s_opuid','15'),(12,'smart_order_settle_complete','14','s_opuid','14'),(23,'smart_promotion_plan','6','pp_content',''),(24,'smart_promotion_plan','6','pp_content_m',''),(646,'smart_product','U4670-E5063-E9563','p_guide_type_10','list'),(586,'smart_product','A1293-C2219-A8817','p_guide_type_30','none'),(584,'smart_product','A1293-C2219-A8817','p_guide_type_20','none'),(565,'smart_product','A2453-Y9662-J6064','p_guide_uid_10','8'),(566,'smart_product','A2453-Y9662-J6064','p_guide_type_20','none'),(567,'smart_product','A2453-Y9662-J6064','p_guide_uid_20','7'),(568,'smart_product','A2453-Y9662-J6064','p_guide_type_30','none'),(569,'smart_product','A2453-Y9662-J6064','p_guide_uid_30','6'),(570,'smart_product','A3201-F1498-C8030','p_guide_type_10','none'),(571,'smart_product','A3201-F1498-C8030','p_guide_uid_10','8'),(572,'smart_product','A3201-F1498-C8030','p_guide_type_20','none'),(573,'smart_product','A3201-F1498-C8030','p_guide_uid_20','7'),(574,'smart_product','A3201-F1498-C8030','p_guide_type_30','none'),(575,'smart_product','A3201-F1498-C8030','p_guide_uid_30','6'),(576,'smart_product','A2286-R9937-E4783','p_guide_type_10','none'),(577,'smart_product','A2286-R9937-E4783','p_guide_uid_10','8'),(578,'smart_product','A2286-R9937-E4783','p_guide_type_20','none'),(579,'smart_product','A2286-R9937-E4783','p_guide_uid_20','7'),(580,'smart_product','A2286-R9937-E4783','p_guide_type_30','none'),(581,'smart_product','A2286-R9937-E4783','p_guide_uid_30','6'),(582,'smart_product','A1293-C2219-A8817','p_guide_type_10','none'),(564,'smart_product','A2453-Y9662-J6064','p_guide_type_10','none'),(563,'smart_product','A0040-K3768-N6788','p_guide_uid_30','6'),(562,'smart_product','A0040-K3768-N6788','p_guide_type_30','none'),(561,'smart_product','A0040-K3768-N6788','p_guide_uid_20','7'),(560,'smart_product','A0040-K3768-N6788','p_guide_type_20','none'),(559,'smart_product','A0040-K3768-N6788','p_guide_uid_10','8'),(250,'smart_order_settle_complete','15','s_opuid','14,15'),(251,'smart_order_settle_complete','16','s_opuid','16'),(558,'smart_product','A0040-K3768-N6788','p_guide_type_10','none'),(557,'smart_product','A1812-C9372-W5686','p_guide_uid_30','6'),(556,'smart_product','A1812-C9372-W5686','p_guide_type_30','none'),(555,'smart_product','A1812-C9372-W5686','p_guide_uid_20','7'),(554,'smart_product','A1812-C9372-W5686','p_guide_type_20','none'),(553,'smart_product','A1812-C9372-W5686','p_guide_uid_10','8'),(552,'smart_product','A1812-C9372-W5686','p_guide_type_10','none'),(537,'smart_product','A0515-Q4337-S4413','p_guide_uid_20','7'),(538,'smart_product','A0515-Q4337-S4413','p_guide_type_30','none'),(539,'smart_product','A0515-Q4337-S4413','p_guide_uid_30','6'),(540,'smart_product','A2273-B5415-T0647','p_guide_type_10','none'),(541,'smart_product','A2273-B5415-T0647','p_guide_uid_10','8'),(542,'smart_product','A2273-B5415-T0647','p_guide_type_20','none'),(543,'smart_product','A2273-B5415-T0647','p_guide_uid_20','7'),(544,'smart_product','A2273-B5415-T0647','p_guide_type_30','none'),(545,'smart_product','A2273-B5415-T0647','p_guide_uid_30','6'),(546,'smart_product','A1371-M8483-I4297','p_guide_type_10','none'),(547,'smart_product','A1371-M8483-I4297','p_guide_uid_10','8'),(548,'smart_product','A1371-M8483-I4297','p_guide_type_20','none'),(549,'smart_product','A1371-M8483-I4297','p_guide_uid_20','7'),(550,'smart_product','A1371-M8483-I4297','p_guide_type_30','none'),(551,'smart_product','A1371-M8483-I4297','p_guide_uid_30','6'),(536,'smart_product','A0515-Q4337-S4413','p_guide_type_20','none'),(535,'smart_product','A0515-Q4337-S4413','p_guide_uid_10','8'),(534,'smart_product','A0515-Q4337-S4413','p_guide_type_10','none'),(533,'smart_product','A1245-L2995-F9638','p_guide_uid_30','6'),(532,'smart_product','A1245-L2995-F9638','p_guide_type_30','none'),(316,'smart_order_settle_complete','17','s_opuid','5879,5880'),(317,'smart_order_settle_complete','18','s_opuid','5879,5880'),(531,'smart_product','A1245-L2995-F9638','p_guide_uid_20','7'),(530,'smart_product','A1245-L2995-F9638','p_guide_type_20','none'),(529,'smart_product','A1245-L2995-F9638','p_guide_uid_10','8'),(528,'smart_product','A1245-L2995-F9638','p_guide_type_10','none'),(527,'smart_product','A2846-Q8684-N5066','p_guide_uid_30','6'),(526,'smart_product','A2846-Q8684-N5066','p_guide_type_30','none'),(525,'smart_product','A2846-Q8684-N5066','p_guide_uid_20','7'),(524,'smart_product','A2846-Q8684-N5066','p_guide_type_20','none'),(637,'smart_product','D5568-V9700-L4557','p_guide_type_10','list'),(510,'smart_product','A0005-X0863-B7658','p_guide_type_10','none'),(511,'smart_product','A0005-X0863-B7658','p_guide_uid_10','8'),(512,'smart_product','A0005-X0863-B7658','p_guide_type_20','none'),(513,'smart_product','A0005-X0863-B7658','p_guide_uid_20','7'),(514,'smart_product','A0005-X0863-B7658','p_guide_type_30','none'),(515,'smart_product','A0005-X0863-B7658','p_guide_uid_30','6'),(516,'smart_product','A1565-W9572-Y7035','p_guide_type_10','none'),(517,'smart_product','A1565-W9572-Y7035','p_guide_uid_10','8'),(518,'smart_product','A1565-W9572-Y7035','p_guide_type_20','none'),(519,'smart_product','A1565-W9572-Y7035','p_guide_uid_20','7'),(520,'smart_product','A1565-W9572-Y7035','p_guide_type_30','none'),(521,'smart_product','A1565-W9572-Y7035','p_guide_uid_30','6'),(522,'smart_product','A2846-Q8684-N5066','p_guide_type_10','none'),(523,'smart_product','A2846-Q8684-N5066','p_guide_uid_10','8'),(508,'smart_product','A0369-A2723-E5596','p_guide_type_30','none'),(506,'smart_product','A0369-A2723-E5596','p_guide_type_20','none'),(883,'smart_product','C6364-U7256-J4618','p_guide_uid_10','25'),(504,'smart_product','A0369-A2723-E5596','p_guide_type_10','none'),(233,'smart_promotion_plan','9','pp_content_m',''),(502,'smart_product','A0344-N7288-E3075','p_guide_type_30','none'),(232,'smart_promotion_plan','9','pp_content','<p><img src=\\\"/upfiles/smarteditor/22.jpg\\\" title=\\\"22.jpg\\\"><br style=\\\"clear:both;\\\">&nbsp;</p>'),(231,'smart_promotion_plan','8','pp_content_m',''),(229,'smart_promotion_plan','7','pp_content_m',''),(230,'smart_promotion_plan','8','pp_content',''),(228,'smart_promotion_plan','7','pp_content',''),(500,'smart_product','A0344-N7288-E3075','p_guide_type_20','none'),(881,'smart_product','H7822-Q6889-R6001','p_guide_uid_10','25'),(882,'smart_product','U8905-L6749-Y3508','p_guide_uid_10','25'),(498,'smart_product','A0344-N7288-E3075','p_guide_type_10','none'),(497,'smart_product','A3026-T2024-G1998','p_guide_uid_30','6'),(496,'smart_product','A3026-T2024-G1998','p_guide_type_30','none'),(495,'smart_product','A3026-T2024-G1998','p_guide_uid_20','7'),(494,'smart_product','A3026-T2024-G1998','p_guide_type_20','none'),(493,'smart_product','A3026-T2024-G1998','p_guide_uid_10','8'),(492,'smart_product','A3026-T2024-G1998','p_guide_type_10','none'),(634,'smart_product','W0678-D3642-R9462','p_guide_type_10','list'),(490,'smart_product','A2476-K3606-A0582','p_guide_type_30','none'),(840,'smart_product','A2476-K3606-A0582','p_guide_uid_10','25'),(488,'smart_product','A2476-K3606-A0582','p_guide_type_20','none'),(474,'smart_product','A1132-Y0020-I9894','p_guide_type_10','none'),(475,'smart_product','A1132-Y0020-I9894','p_guide_uid_10','8'),(476,'smart_product','A1132-Y0020-I9894','p_guide_type_20','none'),(477,'smart_product','A1132-Y0020-I9894','p_guide_uid_20','7'),(478,'smart_product','A1132-Y0020-I9894','p_guide_type_30','none'),(479,'smart_product','A1132-Y0020-I9894','p_guide_uid_30','6'),(480,'smart_product','A1230-M4991-J3859','p_guide_type_10','none'),(481,'smart_product','A1230-M4991-J3859','p_guide_uid_10','8'),(482,'smart_product','A1230-M4991-J3859','p_guide_type_20','none'),(483,'smart_product','A1230-M4991-J3859','p_guide_uid_20','7'),(484,'smart_product','A1230-M4991-J3859','p_guide_type_30','none'),(485,'smart_product','A1230-M4991-J3859','p_guide_uid_30','6'),(486,'smart_product','A2476-K3606-A0582','p_guide_type_10','none'),(454,'smart_product','A3360-Z7477-M6326','p_guide_type_30','none'),(636,'smart_product','W0678-D3642-R9462','p_guide_type_30','list'),(456,'smart_product','A0851-Y4325-F5506','p_guide_type_10','none'),(457,'smart_product','A0851-Y4325-F5506','p_guide_uid_10','8'),(458,'smart_product','A0851-Y4325-F5506','p_guide_type_20','none'),(459,'smart_product','A0851-Y4325-F5506','p_guide_uid_20','7'),(460,'smart_product','A0851-Y4325-F5506','p_guide_type_30','none'),(462,'smart_product','A1561-R7770-B0752','p_guide_type_10','none'),(891,'smart_product','B5485-Q8863-C2763','p_guide_uid_10','25'),(464,'smart_product','A1561-R7770-B0752','p_guide_type_20','none'),(890,'smart_product','U8131-A5406-B1395','p_guide_uid_10','25'),(466,'smart_product','A1561-R7770-B0752','p_guide_type_30','none'),(635,'smart_product','W0678-D3642-R9462','p_guide_type_20','list'),(468,'smart_product','A1853-T6120-V7609','p_guide_type_10','none'),(470,'smart_product','A1853-T6120-V7609','p_guide_type_20','none'),(841,'smart_product','A1853-T6120-V7609','p_guide_uid_10','25'),(472,'smart_product','A1853-T6120-V7609','p_guide_type_30','none'),(888,'smart_product','B9998-B2904-D7348','p_guide_uid_10','25'),(452,'smart_product','A3360-Z7477-M6326','p_guide_type_20','none'),(889,'smart_product','B2776-A7254-M2918','p_guide_uid_10','25'),(450,'smart_product','A3360-Z7477-M6326','p_guide_type_10','none'),(448,'smart_product','A2939-W4908-C9360','p_guide_type_30','none'),(884,'smart_product','U4670-E5063-E9563','p_guide_uid_10','25'),(446,'smart_product','A2939-W4908-C9360','p_guide_type_20','none'),(885,'smart_product','D0738-M0176-Y5381','p_guide_uid_10','25'),(444,'smart_product','A2939-W4908-C9360','p_guide_type_10','none'),(442,'smart_product','A2668-K4581-X3347','p_guide_type_30','none'),(638,'smart_product','D5568-V9700-L4557','p_guide_type_20','list'),(348,'smart_order_settle_complete','19','s_opuid','5879,5880'),(373,'smart_order_settle_complete','20','s_opuid','5927'),(374,'smart_order_settle_complete','21','s_opuid','5928,5929'),(375,'smart_order_settle_complete','22','s_opuid','5985,5986'),(427,'smart_product','A1404-E8574-G1535','p_guide_uid_10','8'),(428,'smart_product','A1404-E8574-G1535','p_guide_type_20','none'),(429,'smart_product','A1404-E8574-G1535','p_guide_uid_20','7'),(430,'smart_product','A1404-E8574-G1535','p_guide_type_30','none'),(432,'smart_product','A1352-S9931-R9011','p_guide_type_10','none'),(433,'smart_product','A1352-S9931-R9011','p_guide_uid_10','8'),(434,'smart_product','A1352-S9931-R9011','p_guide_type_20','none'),(435,'smart_product','A1352-S9931-R9011','p_guide_uid_20','7'),(436,'smart_product','A1352-S9931-R9011','p_guide_type_30','none'),(437,'smart_product','A1352-S9931-R9011','p_guide_uid_30','6'),(438,'smart_product','A2668-K4581-X3347','p_guide_type_10','manual'),(440,'smart_product','A2668-K4581-X3347','p_guide_type_20','none'),(426,'smart_product','A1404-E8574-G1535','p_guide_type_10','list'),(619,'smart_product','B9998-B2904-D7348','p_guide_type_10','list'),(424,'smart_product','A3069-L9262-I4831','p_guide_type_30','none'),(848,'smart_product','A3069-L9262-I4831','p_guide_uid_10','25'),(422,'smart_product','A3069-L9262-I4831','p_guide_type_20','none'),(383,'smart_order_settle_complete','23','s_opuid','6092'),(420,'smart_product','A3069-L9262-I4831','p_guide_type_10','none'),(419,'smart_product','A2256-T9783-H4063','p_guide_uid_30','6'),(418,'smart_product','A2256-T9783-H4063','p_guide_type_30','none'),(417,'smart_product','A2256-T9783-H4063','p_guide_uid_20','7'),(416,'smart_product','A2256-T9783-H4063','p_guide_type_20','none'),(415,'smart_product','A2256-T9783-H4063','p_guide_uid_10','8'),(414,'smart_product','A2256-T9783-H4063','p_guide_type_10','none'),(413,'smart_product','A2624-P0983-A1168','p_guide_uid_30','6'),(412,'smart_product','A2624-P0983-A1168','p_guide_type_30','none'),(411,'smart_product','A2624-P0983-A1168','p_guide_uid_20','7'),(410,'smart_product','A2624-P0983-A1168','p_guide_type_20','none'),(408,'smart_product','A2624-P0983-A1168','p_guide_type_10','none'),(409,'smart_product','A2624-P0983-A1168','p_guide_uid_10','8'),(664,'smart_product','A2668-K4581-X3347','p_guide_10','<p>ㅇ 배송비 : 기본배송료는 2,500원이며 상품에 따라 완전 또는 조건부 무료배송일 수 있습니다. (도서,산간,오지 일부지역은 배송비가 추가될 수 있습니다)</p><p>ㅇ 본 상품의 평균 배송일은 결제완료 후 평일기준 3일입니다.</p><p>&nbsp; - 배송지연 시 SMS로 안내 드립니다.<br></p><p>&nbsp; - 발송 후 수령하시는 지역 택배영업소의 상황에 따라 다소 차이가 있을 수 있습니다.<br></p><p>&nbsp; - 주말 및 공휴일은 발송하지 않습니다.<br></p>'),(652,'smart_promotion_plan','15','pp_content','<p style=\\\"text-align: center; \\\" align=\\\"center\\\"><br></p>'),(616,'smart_product','H7822-Q6889-R6001','p_guide_type_10','list'),(608,'smart_product','J6851-A0141-T1461','p_guide_type_10','list'),(609,'smart_product','J6851-A0141-T1461','p_guide_type_20','list'),(610,'smart_product','J6851-A0141-T1461','p_guide_type_30','list'),(613,'smart_product','B2776-A7254-M2918','p_guide_type_10','list'),(614,'smart_product','B2776-A7254-M2918','p_guide_type_20','list'),(615,'smart_product','B2776-A7254-M2918','p_guide_type_30','list'),(617,'smart_product','H7822-Q6889-R6001','p_guide_type_20','list'),(618,'smart_product','H7822-Q6889-R6001','p_guide_type_30','list'),(620,'smart_product','B9998-B2904-D7348','p_guide_type_20','list'),(621,'smart_product','B9998-B2904-D7348','p_guide_type_30','list'),(622,'smart_product','S5518-V0905-E2496','p_guide_type_10','list'),(623,'smart_product','S5518-V0905-E2496','p_guide_type_20','list'),(624,'smart_product','S5518-V0905-E2496','p_guide_type_30','list'),(625,'smart_product','Q9820-Q1955-T2594','p_guide_type_10','list'),(626,'smart_product','Q9820-Q1955-T2594','p_guide_type_20','list'),(627,'smart_product','Q9820-Q1955-T2594','p_guide_type_30','list'),(628,'smart_product','S9950-E6332-A4864','p_guide_type_10','list'),(629,'smart_product','S9950-E6332-A4864','p_guide_type_20','list'),(630,'smart_product','S9950-E6332-A4864','p_guide_type_30','list'),(631,'smart_product','D0738-M0176-Y5381','p_guide_type_10','list'),(632,'smart_product','D0738-M0176-Y5381','p_guide_type_20','list'),(633,'smart_product','D0738-M0176-Y5381','p_guide_type_30','list'),(639,'smart_product','D5568-V9700-L4557','p_guide_type_30','list'),(640,'smart_product','C6364-U7256-J4618','p_guide_type_10','list'),(641,'smart_product','C6364-U7256-J4618','p_guide_type_20','list'),(642,'smart_product','C6364-U7256-J4618','p_guide_type_30','list'),(643,'smart_product','U8905-L6749-Y3508','p_guide_type_10','list'),(644,'smart_product','U8905-L6749-Y3508','p_guide_type_20','list'),(645,'smart_product','U8905-L6749-Y3508','p_guide_type_30','list'),(647,'smart_product','U4670-E5063-E9563','p_guide_type_20','list'),(648,'smart_product','U4670-E5063-E9563','p_guide_type_30','list'),(887,'smart_product','S5518-V0905-E2496','p_guide_uid_10','25'),(886,'smart_product','S9950-E6332-A4864','p_guide_uid_10','25'),(651,'smart_product','Q9820-Q1955-T2594','p_guide_30','<p><br></p>'),(665,'smart_product','A2668-K4581-X3347','p_guide_20','<p>ㅇ 교환/반품/환불이 가능한 경우입니다.</p>'),(656,'smart_promotion_plan','13','pp_content','<p style=\\\"text-align: center; \\\" align=\\\"center\\\"><br></p>'),(895,'smart_promotion_plan','13','pp_content_m',''),(715,'smart_product','W0678-D3642-R9462','p_guide_uid_10','25'),(666,'smart_product','A2668-K4581-X3347','p_guide_30','<p>ㅇ 교환/반품/환불이 불가능한 경우입니다.<br></p>'),(696,'smart_product','Z7913-S9943-R3804','p_guide_type_20','list'),(695,'smart_product','Z7913-S9943-R3804','p_guide_type_30','list'),(694,'smart_product','B5485-Q8863-C2763','p_guide_type_30','list'),(693,'smart_product','B5485-Q8863-C2763','p_guide_type_20','list'),(692,'smart_product','B5485-Q8863-C2763','p_guide_type_10','list'),(680,'smart_product','X8000-P0254-T3646','p_guide_type_30','list'),(681,'smart_product','X8000-P0254-T3646','p_guide_type_10','list'),(682,'smart_product','X8000-P0254-T3646','p_guide_type_20','list'),(691,'smart_product','U8131-A5406-B1395','p_guide_type_30','list'),(690,'smart_product','U8131-A5406-B1395','p_guide_type_20','list'),(689,'smart_product','U8131-A5406-B1395','p_guide_type_10','list'),(688,'smart_order_settle_complete','2','s_opuid','48,49,50'),(679,'smart_product','P9352-A0491-I1021','p_guide_type_30','list'),(677,'smart_product','P9352-A0491-I1021','p_guide_type_10','list'),(678,'smart_product','P9352-A0491-I1021','p_guide_type_20','list'),(687,'smart_order_settle_complete','1','s_opuid','46'),(697,'smart_product','Z7913-S9943-R3804','p_guide_type_10','list'),(838,'smart_product','A2668-K4581-X3347','p_guide_uid_10','25'),(726,'smart_product','N2250-P9671-A2202','p_guide_type_30','list'),(727,'smart_product','N2250-P9671-A2202','p_guide_type_10','list'),(728,'smart_product','N2250-P9671-A2202','p_guide_type_20','list'),(729,'smart_product','J2305-X9551-I2553','p_guide_type_30','list'),(730,'smart_product','J2305-X9551-I2553','p_guide_type_10','list'),(731,'smart_product','J2305-X9551-I2553','p_guide_type_20','list'),(892,'smart_product','Z7913-S9943-R3804','p_guide_uid_10','25'),(893,'smart_product','A0344-N7288-E3075','p_guide_uid_10','25');
/*!40000 ALTER TABLE `smart_table_text` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temp_tb`
--

DROP TABLE IF EXISTS `temp_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_tb` (
  `s_ticket_auto_C` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(카드)',
  `s_ticket_auto_L` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(실시간계좌이체)',
  `s_ticket_auto_B` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(무통장입금)',
  `s_ticket_auto_G` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(전액포인트)',
  `s_ticket_auto_V` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(가상계좌)',
  `s_ticket_auto_H` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(휴대폰)',
  `s_ticket_auto_P` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품자동정산전환(페이코)',
  `s_ticket_auto_on` enum('Y','N') CHARACTER SET utf8 NOT NULL DEFAULT 'N' COMMENT '티켓 정산 자동처리 사용여부',
  `s_ticket_auto_PP` int(3) NOT NULL DEFAULT 0 COMMENT '티켓상품 페이플 간편결제 자동정산 처리 일수 추가'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='삭졔예정';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temp_tb`
--

LOCK TABLES `temp_tb` WRITE;
/*!40000 ALTER TABLE `temp_tb` DISABLE KEYS */;
/*!40000 ALTER TABLE `temp_tb` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-28 10:43:57
