/*
MySQLAdmin Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : apisystem

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2014-12-25 22:51:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for apisys_action
-- ----------------------------
DROP TABLE IF EXISTS `apisys_action`;
CREATE TABLE `apisys_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text NOT NULL COMMENT '行为规则',
  `log` text NOT NULL COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统行为表';

-- ----------------------------
-- Records of apisys_action
-- ----------------------------
INSERT INTO `apisys_action` VALUES ('1', 'user_login', '用户登录', '积分+10，每天一次', 'table:member|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;', '[user|get_nickname]在[time|time_format]登录了后台', '1', '1', '1418981220');
INSERT INTO `apisys_action` VALUES ('2', 'add_article', '发布文章', '积分+5，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:5', '', '2', '0', '1418973180');
INSERT INTO `apisys_action` VALUES ('3', 'review', '评论', '评论积分+1，无限制', 'table:member|field:score|condition:uid={$self}|rule:score+1', '', '2', '1', '1418985646');
INSERT INTO `apisys_action` VALUES ('4', 'add_document', '发表文档', '积分+10，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+10|cycle:24|max:5', '[user|get_nickname]在[time|time_format]发表了一篇文章。\r\n表[model]，记录编号[record]。', '2', '0', '1418939726');
INSERT INTO `apisys_action` VALUES ('5', 'add_document_topic', '发表讨论', '积分+5，每天上限10次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:10', '', '2', '0', '1418985551');
INSERT INTO `apisys_action` VALUES ('6', 'update_config', '更新配置', '新增或修改或删除配置', '', '', '1', '1', '1418994988');
INSERT INTO `apisys_action` VALUES ('7', 'update_model', '更新模型', '新增或修改模型', '', '', '1', '1', '1418995057');
INSERT INTO `apisys_action` VALUES ('8', 'update_attribute', '更新属性', '新增或更新或删除属性', '', '', '1', '1', '1418995963');
INSERT INTO `apisys_action` VALUES ('9', 'update_channel', '更新导航', '新增或修改或删除导航', '', '', '1', '1', '1418996301');
INSERT INTO `apisys_action` VALUES ('10', 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', '1', '1', '1418996392');
INSERT INTO `apisys_action` VALUES ('11', 'update_category', '更新分类', '新增或修改或删除分类', '', '', '1', '1', '1418996765');
INSERT INTO `apisys_action` VALUES ('12', 'update_apidoc', ' 更新Api文档', ' 更新Api文档', '', '', '2', '1', '1418943679');

-- ----------------------------
-- Table structure for apisys_action_log
-- ----------------------------
DROP TABLE IF EXISTS `apisys_action_log`;
CREATE TABLE `apisys_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表';

-- ----------------------------
-- Records of apisys_action_log
-- ----------------------------
INSERT INTO `apisys_action_log` VALUES ('1', '9', '1', '1418906433', 'channel', '6', '操作url：/index.php?s=/Admin/Channel/edit.html', '1', '1418967544');
INSERT INTO `apisys_action_log` VALUES ('2', '9', '1', '1418906433', 'channel', '6', '操作url：/index.php?s=/Admin/Channel/edit.html', '1', '1418967810');
INSERT INTO `apisys_action_log` VALUES ('3', '1', '1', '2130706433', 'member', '1', 'admin在2017-03-29 14:49登录了后台', '1', '1490770172');

-- ----------------------------
-- Table structure for apisys_addons
-- ----------------------------
DROP TABLE IF EXISTS `apisys_addons`;
CREATE TABLE `apisys_addons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='插件表';

-- ----------------------------
-- Records of apisys_addons
-- ----------------------------
INSERT INTO `apisys_addons` VALUES ('15', 'EditorForAdmin', '后台编辑器', '用于增强整站长文本的输入和显示', '1', '{\"editor_type\":\"2\",\"editor_wysiwyg\":\"1\",\"editor_height\":\"500px\",\"editor_resize_type\":\"1\"}', 'thinkphp', '0.1', '1418926253', '0');
INSERT INTO `apisys_addons` VALUES ('2', 'SiteStat', '站点统计信息', '统计站点的基础信息', '1', '{\"title\":\"\\u7cfb\\u7edf\\u4fe1\\u606f\",\"width\":\"1\",\"display\":\"1\",\"status\":\"0\"}', 'thinkphp', '0.1', '1418912015', '0');
INSERT INTO `apisys_addons` VALUES ('3', 'DevTeam', '开发团队信息', '开发团队成员信息', '1', '{\"title\":\"OneThink\\u5f00\\u53d1\\u56e2\\u961f\",\"width\":\"2\",\"display\":\"1\"}', 'thinkphp', '0.1', '1418912022', '0');
INSERT INTO `apisys_addons` VALUES ('4', 'SystemInfo', '系统环境信息', '用于显示一些服务器的信息', '1', '{\"title\":\"\\u7cfb\\u7edf\\u4fe1\\u606f\",\"width\":\"2\",\"display\":\"1\"}', 'thinkphp', '0.1', '1418912036', '0');
INSERT INTO `apisys_addons` VALUES ('5', 'Editor', '前台编辑器', '用于增强整站长文本的输入和显示', '1', '{\"editor_type\":\"2\",\"editor_wysiwyg\":\"1\",\"editor_height\":\"300px\",\"editor_resize_type\":\"1\"}', 'thinkphp', '0.1', '1418930910', '0');
INSERT INTO `apisys_addons` VALUES ('6', 'Attachment', '附件', '用于文档模型上传附件', '1', 'null', 'thinkphp', '0.1', '1418942319', '1');
INSERT INTO `apisys_addons` VALUES ('9', 'SocialComment', '通用社交化评论', '集成了各种社交化评论插件，轻松集成到系统中。', '1', '{\"comment_type\":\"1\",\"comment_uid_youyan\":\"\",\"comment_short_name_duoshuo\":\"\",\"comment_data_list_duoshuo\":\"\"}', 'thinkphp', '0.1', '1418973962', '0');

-- ----------------------------
-- Table structure for apisys_apilog
-- ----------------------------
DROP TABLE IF EXISTS `apisys_apilog`;
CREATE TABLE `apisys_apilog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) NOT NULL COMMENT '用户行为',
  `ip` varchar(64) NOT NULL COMMENT 'IP',
  `cat_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'API-ID',
  `content` text NOT NULL COMMENT '日志内容',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_apilog
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_attachment
-- ----------------------------
DROP TABLE IF EXISTS `apisys_attachment`;
CREATE TABLE `apisys_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '附件显示名',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型',
  `source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源ID',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '附件大小',
  `dir` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '上级目录ID',
  `sort` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `idx_record_status` (`record_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';

-- ----------------------------
-- Records of apisys_attachment
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_attribute
-- ----------------------------
DROP TABLE IF EXISTS `apisys_attribute`;
CREATE TABLE `apisys_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段注释',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段定义',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '数据类型',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '参数',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `is_must` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必填',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `validate_rule` varchar(255) NOT NULL,
  `validate_time` tinyint(1) unsigned NOT NULL,
  `error_info` varchar(100) NOT NULL,
  `validate_type` varchar(25) NOT NULL,
  `auto_rule` varchar(100) NOT NULL,
  `auto_time` tinyint(1) unsigned NOT NULL,
  `auto_type` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='模型属性表';

-- ----------------------------
-- Records of apisys_attribute
-- ----------------------------
INSERT INTO `apisys_attribute` VALUES ('1', 'uid', '用户ID', 'int(10) unsigned NOT NULL ', 'num', '0', '', '0', '', '1', '0', '1', '1418908362', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('2', 'name', '标识', 'char(40) NOT NULL ', 'string', '', '同一根节点下标识不重复', '1', '', '1', '0', '1', '1418994743', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('3', 'title', '标题', 'char(80) NOT NULL ', 'string', '', '文档标题', '1', '', '1', '0', '1', '1418994778', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('4', 'category_id', '所属分类', 'int(10) unsigned NOT NULL ', 'string', '', '', '0', '', '1', '0', '1', '1418908336', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('5', 'description', '描述', 'char(140) NOT NULL ', 'textarea', '', '', '1', '', '1', '0', '1', '1418994927', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('6', 'root', '根节点', 'int(10) unsigned NOT NULL ', 'num', '0', '该文档的顶级文档编号', '0', '', '1', '0', '1', '1418908323', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('7', 'pid', '所属ID', 'int(10) unsigned NOT NULL ', 'num', '0', '父文档编号', '0', '', '1', '0', '1', '1418908543', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('8', 'model_id', '内容模型ID', 'tinyint(3) unsigned NOT NULL ', 'num', '0', '该文档所对应的模型', '0', '', '1', '0', '1', '1418908350', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('9', 'type', '内容类型', 'tinyint(3) unsigned NOT NULL ', 'select', '2', '', '1', '1:目录\r\n2:主题\r\n3:段落', '1', '0', '1', '1418911157', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('10', 'position', '推荐位', 'smallint(5) unsigned NOT NULL ', 'checkbox', '0', '多个推荐则将其推荐值相加', '1', '1:列表推荐\r\n2:频道页推荐\r\n4:首页推荐', '1', '0', '1', '1418995640', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('11', 'link_id', '外链', 'int(10) unsigned NOT NULL ', 'num', '0', '0-非外链，大于0-外链ID,需要函数进行链接与编号的转换', '1', '', '1', '0', '1', '1418995757', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('12', 'cover_id', '封面', 'int(10) unsigned NOT NULL ', 'picture', '0', '0-无封面，大于0-封面图片ID，需要函数处理', '1', '', '1', '0', '1', '1418947827', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('13', 'display', '可见性', 'tinyint(3) unsigned NOT NULL ', 'radio', '1', '', '1', '0:不可见\r\n1:所有人可见', '1', '0', '1', '1418962271', '1418991233', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `apisys_attribute` VALUES ('14', 'deadline', '截至时间', 'int(10) unsigned NOT NULL ', 'datetime', '0', '0-永久有效', '1', '', '1', '0', '1', '1418963248', '1418991233', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `apisys_attribute` VALUES ('15', 'attach', '附件数量', 'tinyint(3) unsigned NOT NULL ', 'num', '0', '', '0', '', '1', '0', '1', '1418960355', '1418991233', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `apisys_attribute` VALUES ('16', 'view', '浏览量', 'int(10) unsigned NOT NULL ', 'num', '0', '', '1', '', '1', '0', '1', '1418995835', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('17', 'comment', '评论数', 'int(10) unsigned NOT NULL ', 'num', '0', '', '1', '', '1', '0', '1', '1418995846', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('18', 'extend', '扩展统计字段', 'int(10) unsigned NOT NULL ', 'num', '0', '根据需求自行使用', '0', '', '1', '0', '1', '1418908264', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('19', 'level', '优先级', 'int(10) unsigned NOT NULL ', 'num', '0', '越高排序越靠前', '1', '', '1', '0', '1', '1418995894', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('20', 'create_time', '创建时间', 'int(10) unsigned NOT NULL ', 'datetime', '0', '', '1', '', '1', '0', '1', '1418995903', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('21', 'update_time', '更新时间', 'int(10) unsigned NOT NULL ', 'datetime', '0', '', '0', '', '1', '0', '1', '1418908277', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('22', 'status', '数据状态', 'tinyint(4) NOT NULL ', 'radio', '0', '', '0', '-1:删除\r\n0:禁用\r\n1:正常\r\n2:待审核\r\n3:草稿', '1', '0', '1', '1418908496', '1418991233', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('23', 'parse', '内容解析类型', 'tinyint(3) unsigned NOT NULL ', 'select', '0', '', '0', '0:html\r\n1:ubb\r\n2:markdown', '2', '0', '1', '1418911049', '1418991243', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('24', 'content', '文章内容', 'text NOT NULL ', 'editor', '', '', '1', '', '2', '0', '1', '1418996225', '1418991243', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('25', 'template', '详情页显示模板', 'varchar(100) NOT NULL ', 'string', '', '参照display方法参数的定义', '1', '', '2', '0', '1', '1418996190', '1418991243', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('26', 'bookmark', '收藏数', 'int(10) unsigned NOT NULL ', 'num', '0', '', '1', '', '2', '0', '1', '1418996103', '1418991243', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('27', 'parse', '内容解析类型', 'tinyint(3) unsigned NOT NULL ', 'select', '0', '', '0', '0:html\r\n1:ubb\r\n2:markdown', '3', '0', '1', '1418960461', '1418991252', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `apisys_attribute` VALUES ('28', 'content', '下载详细描述', 'text NOT NULL ', 'editor', '', '', '1', '', '3', '0', '1', '1418996438', '1418991252', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('29', 'template', '详情页显示模板', 'varchar(100) NOT NULL ', 'string', '', '', '1', '', '3', '0', '1', '1418996429', '1418991252', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('30', 'file_id', '文件ID', 'int(10) unsigned NOT NULL ', 'file', '0', '需要函数处理', '1', '', '3', '0', '1', '1418996415', '1418991252', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('31', 'download', '下载次数', 'int(10) unsigned NOT NULL ', 'num', '0', '', '1', '', '3', '0', '1', '1418996380', '1418991252', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('32', 'size', '文件大小', 'bigint(20) unsigned NOT NULL ', 'num', '0', '单位bit', '1', '', '3', '0', '1', '1418996371', '1418991252', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('50', 'restring', '返回参数', 'text NOT NULL ', 'textarea', '', '', '1', '', '6', '0', '1', '1418927181', '1418925411', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `apisys_attribute` VALUES ('49', 'request', '请求字段', 'text NOT NULL ', 'textarea', '', '', '1', '', '6', '0', '1', '1418927567', '1418925411', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `apisys_attribute` VALUES ('48', 'apiurl', '接口地址', 'varchar(255) NOT NULL ', 'string', '', '', '1', '', '6', '0', '1', '1418925411', '1418925411', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('58', 'name', '项目分类', 'varchar(20) NOT NULL', 'string', '', '', '1', '', '7', '1', '1', '1418942115', '1418987292', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('44', 'title', '接口名称', 'varchar(50) NOT NULL ', 'string', '', '', '1', '', '6', '0', '1', '1418925411', '1418925411', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('45', 'description', '接口描述', 'varchar(255) NOT NULL ', 'string', '', '', '1', '', '6', '0', '1', '1418925411', '1418925411', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('46', 'class_id', '接口分类', 'int(10) unsigned NOT NULL ', 'string', '0', '', '1', '', '6', '0', '1', '1418925411', '1418925411', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('47', 'method', 'method', 'char(50) NOT NULL ', 'select', 'GET', '', '1', 'GET:GET\r\nPOST:POST', '6', '0', '1', '1418925411', '1418925411', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('51', 'memo', '备注', 'text NOT NULL ', 'textarea', '', '', '1', '', '6', '0', '1', '1418927515', '1418925411', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `apisys_attribute` VALUES ('52', 'keywords', '关键词', 'varchar(50) NOT NULL ', 'string', '', '', '1', '', '6', '0', '1', '1418925411', '1418925411', '', '0', '', '', '', '0', '');
INSERT INTO `apisys_attribute` VALUES ('53', 'project_id', '项目分类', 'int(10) unsigned NOT NULL ', 'string', '0', '', '1', '', '6', '0', '1', '1418987504', '1418925411', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `apisys_attribute` VALUES ('54', 'create_time', '创建时间', 'int(10) NOT NULL', 'datetime', '', '', '1', '', '6', '0', '1', '1418927774', '1418927647', '', '3', '', 'regex', '', '1', 'function');
INSERT INTO `apisys_attribute` VALUES ('55', 'edit_time', '编辑时间', 'int(10) NOT NULL', 'datetime', '', '', '1', '', '6', '0', '1', '1418927749', '1418927719', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('56', 'user_id', '创建者', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '0', '', '6', '0', '1', '1418927861', '1418927861', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('57', 'status', '状态', 'char(50) NOT NULL', 'select', '', '', '1', '0:正常\r\n-1:删除', '6', '0', '1', '1418928757', '1418928757', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('59', 'parent_id', '父ID', 'int(10) UNSIGNED NOT NULL', 'num', '0', '', '1', '', '7', '0', '1', '1418987430', '1418987430', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('60', 'son_ids', '子集合', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '7', '0', '1', '1418988488', '1418987681', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('61', 'category_memo', '分类备注', 'text NOT NULL', 'editor', '', '', '1', '', '7', '0', '1', '1418988917', '1418988917', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('62', 'category_url', '分类URL', 'varchar(255) NOT NULL', 'string', '', '顶级分类填写（域名/分类),下级不要斜线只写（页面名）', '1', '', '7', '1', '1', '1418991354', '1418991354', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('63', 'status', '状态', 'int(10) UNSIGNED NOT NULL', 'num', '1', '', '1', '', '7', '0', '1', '1418942156', '1418942156', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('64', 'response', '返回字段', 'text NOT NULL', 'textarea', '', '', '1', '', '6', '0', '1', '1418929334', '1418929334', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('65', 'uid', '用户ID', 'int(10) UNSIGNED NOT NULL', 'num', '0', '用户id', '1', '', '8', '0', '1', '1418954095', '1418937393', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('66', 'content', '日志内容', 'text NOT NULL', 'textarea', '', '', '1', '', '8', '0', '1', '1418954090', '1418937650', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('67', 'aid', 'API-ID', 'int(10) UNSIGNED NOT NULL', 'num', '0', 'API-ID', '1', '', '8', '0', '1', '1418954084', '1418938194', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('68', 'createtime', '创建时间', 'int(10) NOT NULL', 'datetime', '0', '', '1', '', '8', '0', '1', '1418954077', '1418938305', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('69', 'cat_id', '分类ID', 'int(10) UNSIGNED NOT NULL', 'num', '0', '', '1', '', '8', '0', '1', '1418954047', '1418939493', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('70', 'ip', 'IP', 'varchar(64) NOT NULL', 'string', '', '', '1', '', '8', '0', '1', '1418954042', '1418949861', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `apisys_attribute` VALUES ('71', 'action', '用户行为', 'varchar(50) NOT NULL', 'string', '', '', '1', '', '8', '0', '1', '1418954034', '1418950759', '', '3', '', 'regex', '', '3', 'function');

-- ----------------------------
-- Table structure for apisys_auth_extend
-- ----------------------------
DROP TABLE IF EXISTS `apisys_auth_extend`;
CREATE TABLE `apisys_auth_extend` (
  `group_id` mediumint(10) unsigned NOT NULL COMMENT '用户id',
  `extend_id` mediumint(8) unsigned NOT NULL COMMENT '扩展表中数据的id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '扩展类型标识 1:栏目分类权限;2:模型权限',
  UNIQUE KEY `group_extend_type` (`group_id`,`extend_id`,`type`),
  KEY `uid` (`group_id`),
  KEY `group_id` (`extend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组与分类的对应关系表';

-- ----------------------------
-- Records of apisys_auth_extend
-- ----------------------------
INSERT INTO `apisys_auth_extend` VALUES ('1', '1', '2');
INSERT INTO `apisys_auth_extend` VALUES ('1', '2', '2');
INSERT INTO `apisys_auth_extend` VALUES ('1', '3', '2');

-- ----------------------------
-- Table structure for apisys_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `apisys_auth_group`;
CREATE TABLE `apisys_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_auth_group
-- ----------------------------
INSERT INTO `apisys_auth_group` VALUES ('1', 'admin', '1', '注册会员', '没有功能,没有前台和后台功能', '1', '');
INSERT INTO `apisys_auth_group` VALUES ('2', 'admin', '1', '系统管理员', '拥有后台和前台所有功能,admin是超级管理员', '1', '1,2,3,4,5,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,81,82,83,84,86,87,88,89,90,91,92,93,94,95,100,102,103,107,108,109,110,195,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,229,231,232');
INSERT INTO `apisys_auth_group` VALUES ('3', 'admin', '1', '接口维护员', '部分后台功能有前台读写功能', '1', '3,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,88,94,95,107,108,109,110,217,218,219,220,221,222,223,224,225,229,231,232,233');
INSERT INTO `apisys_auth_group` VALUES ('4', 'admin', '1', '前端使用者', '无前台功能，前台只读', '1', '217,218,221,222,223,229,231,235');

-- ----------------------------
-- Table structure for apisys_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `apisys_auth_group_access`;
CREATE TABLE `apisys_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_auth_group_access
-- ----------------------------
INSERT INTO `apisys_auth_group_access` VALUES ('2', '2');
INSERT INTO `apisys_auth_group_access` VALUES ('2', '3');
INSERT INTO `apisys_auth_group_access` VALUES ('3', '4');

-- ----------------------------
-- Table structure for apisys_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `apisys_auth_rule`;
CREATE TABLE `apisys_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_auth_rule
-- ----------------------------
INSERT INTO `apisys_auth_rule` VALUES ('1', 'admin', '2', 'Admin/Index/index', '首页', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('2', 'admin', '2', 'Admin/Article/mydocument', '内容', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('3', 'admin', '2', 'Admin/User/index', '用户', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('4', 'admin', '2', 'Admin/Addons/index', '扩展', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('5', 'admin', '2', 'Admin/Config/group', '系统', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('7', 'admin', '1', 'Admin/article/add', '新增', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('8', 'admin', '1', 'Admin/article/edit', '编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('9', 'admin', '1', 'Admin/article/setStatus', '改变状态', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('10', 'admin', '1', 'Admin/article/update', '保存', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('11', 'admin', '1', 'Admin/article/autoSave', '保存草稿', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('12', 'admin', '1', 'Admin/article/move', '移动', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('13', 'admin', '1', 'Admin/article/copy', '复制', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('14', 'admin', '1', 'Admin/article/paste', '粘贴', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('15', 'admin', '1', 'Admin/article/permit', '还原', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('16', 'admin', '1', 'Admin/article/clear', '清空', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('17', 'admin', '1', 'Admin/article/index', '文档列表', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('18', 'admin', '1', 'Admin/article/recycle', '回收站', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('19', 'admin', '1', 'Admin/User/addaction', '新增用户行为', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('20', 'admin', '1', 'Admin/User/editaction', '编辑用户行为', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('21', 'admin', '1', 'Admin/User/saveAction', '保存用户行为', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('22', 'admin', '1', 'Admin/User/setStatus', '变更行为状态', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('23', 'admin', '1', 'Admin/User/changeStatus?method=forbidUser', '禁用会员', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('24', 'admin', '1', 'Admin/User/changeStatus?method=resumeUser', '启用会员', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('25', 'admin', '1', 'Admin/User/changeStatus?method=deleteUser', '删除会员', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('26', 'admin', '1', 'Admin/User/index', '用户信息', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('27', 'admin', '1', 'Admin/User/action', '用户行为', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('28', 'admin', '1', 'Admin/AuthManager/changeStatus?method=deleteGroup', '删除', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('29', 'admin', '1', 'Admin/AuthManager/changeStatus?method=forbidGroup', '禁用', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('30', 'admin', '1', 'Admin/AuthManager/changeStatus?method=resumeGroup', '恢复', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('31', 'admin', '1', 'Admin/AuthManager/createGroup', '新增', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('32', 'admin', '1', 'Admin/AuthManager/editGroup', '编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('33', 'admin', '1', 'Admin/AuthManager/writeGroup', '保存用户组', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('34', 'admin', '1', 'Admin/AuthManager/group', '授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('35', 'admin', '1', 'Admin/AuthManager/access', '访问授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('36', 'admin', '1', 'Admin/AuthManager/user', '成员授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('37', 'admin', '1', 'Admin/AuthManager/removeFromGroup', '解除授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('38', 'admin', '1', 'Admin/AuthManager/addToGroup', '保存成员授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('39', 'admin', '1', 'Admin/AuthManager/category', '分类授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('40', 'admin', '1', 'Admin/AuthManager/addToCategory', '保存分类授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('41', 'admin', '1', 'Admin/AuthManager/index', '权限管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('42', 'admin', '1', 'Admin/Addons/create', '创建', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('43', 'admin', '1', 'Admin/Addons/checkForm', '检测创建', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('44', 'admin', '1', 'Admin/Addons/preview', '预览', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('45', 'admin', '1', 'Admin/Addons/build', '快速生成插件', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('46', 'admin', '1', 'Admin/Addons/config', '设置', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('47', 'admin', '1', 'Admin/Addons/disable', '禁用', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('48', 'admin', '1', 'Admin/Addons/enable', '启用', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('49', 'admin', '1', 'Admin/Addons/install', '安装', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('50', 'admin', '1', 'Admin/Addons/uninstall', '卸载', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('51', 'admin', '1', 'Admin/Addons/saveconfig', '更新配置', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('52', 'admin', '1', 'Admin/Addons/adminList', '插件后台列表', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('53', 'admin', '1', 'Admin/Addons/execute', 'URL方式访问插件', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('54', 'admin', '1', 'Admin/Addons/index', '插件管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('55', 'admin', '1', 'Admin/Addons/hooks', '钩子管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('56', 'admin', '1', 'Admin/model/add', '新增', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('57', 'admin', '1', 'Admin/model/edit', '编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('58', 'admin', '1', 'Admin/model/setStatus', '改变状态', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('59', 'admin', '1', 'Admin/model/update', '保存数据', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('60', 'admin', '1', 'Admin/Model/index', '模型管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('61', 'admin', '1', 'Admin/Config/edit', '编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('62', 'admin', '1', 'Admin/Config/del', '删除', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('63', 'admin', '1', 'Admin/Config/add', '新增', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('64', 'admin', '1', 'Admin/Config/save', '保存', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('65', 'admin', '1', 'Admin/Config/group', '网站设置', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('66', 'admin', '1', 'Admin/Config/index', '配置管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('67', 'admin', '1', 'Admin/Channel/add', '新增', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('68', 'admin', '1', 'Admin/Channel/edit', '编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('69', 'admin', '1', 'Admin/Channel/del', '删除', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('70', 'admin', '1', 'Admin/Channel/index', '导航管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('71', 'admin', '1', 'Admin/Category/edit', '编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('72', 'admin', '1', 'Admin/Category/add', '新增', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('73', 'admin', '1', 'Admin/Category/remove', '删除', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('74', 'admin', '1', 'Admin/Category/index', '分类管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('75', 'admin', '1', 'Admin/file/upload', '上传控件', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('76', 'admin', '1', 'Admin/file/uploadPicture', '上传图片', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('77', 'admin', '1', 'Admin/file/download', '下载', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('94', 'admin', '1', 'Admin/AuthManager/modelauth', '模型授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('79', 'admin', '1', 'Admin/article/batchOperate', '导入', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('80', 'admin', '1', 'Admin/Database/index?type=export', '备份数据库', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('81', 'admin', '1', 'Admin/Database/index?type=import', '还原数据库', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('82', 'admin', '1', 'Admin/Database/export', '备份', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('83', 'admin', '1', 'Admin/Database/optimize', '优化表', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('84', 'admin', '1', 'Admin/Database/repair', '修复表', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('86', 'admin', '1', 'Admin/Database/import', '恢复', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('87', 'admin', '1', 'Admin/Database/del', '删除', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('88', 'admin', '1', 'Admin/User/add', '新增用户', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('89', 'admin', '1', 'Admin/Attribute/index', '属性管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('90', 'admin', '1', 'Admin/Attribute/add', '新增', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('91', 'admin', '1', 'Admin/Attribute/edit', '编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('92', 'admin', '1', 'Admin/Attribute/setStatus', '改变状态', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('93', 'admin', '1', 'Admin/Attribute/update', '保存数据', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('95', 'admin', '1', 'Admin/AuthManager/addToModel', '保存模型授权', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('96', 'admin', '1', 'Admin/Category/move', '移动', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('97', 'admin', '1', 'Admin/Category/merge', '合并', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('98', 'admin', '1', 'Admin/Config/menu', '后台菜单管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('99', 'admin', '1', 'Admin/Article/mydocument', '内容', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('100', 'admin', '1', 'Admin/Menu/index', '菜单管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('101', 'admin', '1', 'Admin/other', '其他', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('102', 'admin', '1', 'Admin/Menu/add', '新增', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('103', 'admin', '1', 'Admin/Menu/edit', '编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('104', 'admin', '1', 'Admin/Think/lists?model=article', '文章管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('105', 'admin', '1', 'Admin/Think/lists?model=download', '下载管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('106', 'admin', '1', 'Admin/Think/lists?model=config', '配置管理', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('107', 'admin', '1', 'Admin/Action/actionlog', '行为日志', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('108', 'admin', '1', 'Admin/User/updatePassword', '修改密码', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('109', 'admin', '1', 'Admin/User/updateNickname', '修改昵称', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('110', 'admin', '1', 'Admin/action/edit', '查看行为日志', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('205', 'admin', '1', 'Admin/think/add', '新增数据', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('111', 'admin', '2', 'Admin/article/index', '文档列表', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('112', 'admin', '2', 'Admin/article/add', '新增', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('113', 'admin', '2', 'Admin/article/edit', '编辑', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('114', 'admin', '2', 'Admin/article/setStatus', '改变状态', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('115', 'admin', '2', 'Admin/article/update', '保存', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('116', 'admin', '2', 'Admin/article/autoSave', '保存草稿', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('117', 'admin', '2', 'Admin/article/move', '移动', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('118', 'admin', '2', 'Admin/article/copy', '复制', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('119', 'admin', '2', 'Admin/article/paste', '粘贴', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('120', 'admin', '2', 'Admin/article/batchOperate', '导入', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('121', 'admin', '2', 'Admin/article/recycle', '回收站', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('122', 'admin', '2', 'Admin/article/permit', '还原', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('123', 'admin', '2', 'Admin/article/clear', '清空', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('124', 'admin', '2', 'Admin/User/add', '新增用户', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('125', 'admin', '2', 'Admin/User/action', '用户行为', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('126', 'admin', '2', 'Admin/User/addAction', '新增用户行为', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('127', 'admin', '2', 'Admin/User/editAction', '编辑用户行为', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('128', 'admin', '2', 'Admin/User/saveAction', '保存用户行为', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('129', 'admin', '2', 'Admin/User/setStatus', '变更行为状态', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('130', 'admin', '2', 'Admin/User/changeStatus?method=forbidUser', '禁用会员', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('131', 'admin', '2', 'Admin/User/changeStatus?method=resumeUser', '启用会员', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('132', 'admin', '2', 'Admin/User/changeStatus?method=deleteUser', '删除会员', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('133', 'admin', '2', 'Admin/AuthManager/index', '权限管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('134', 'admin', '2', 'Admin/AuthManager/changeStatus?method=deleteGroup', '删除', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('135', 'admin', '2', 'Admin/AuthManager/changeStatus?method=forbidGroup', '禁用', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('136', 'admin', '2', 'Admin/AuthManager/changeStatus?method=resumeGroup', '恢复', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('137', 'admin', '2', 'Admin/AuthManager/createGroup', '新增', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('138', 'admin', '2', 'Admin/AuthManager/editGroup', '编辑', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('139', 'admin', '2', 'Admin/AuthManager/writeGroup', '保存用户组', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('140', 'admin', '2', 'Admin/AuthManager/group', '授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('141', 'admin', '2', 'Admin/AuthManager/access', '访问授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('142', 'admin', '2', 'Admin/AuthManager/user', '成员授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('143', 'admin', '2', 'Admin/AuthManager/removeFromGroup', '解除授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('144', 'admin', '2', 'Admin/AuthManager/addToGroup', '保存成员授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('145', 'admin', '2', 'Admin/AuthManager/category', '分类授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('146', 'admin', '2', 'Admin/AuthManager/addToCategory', '保存分类授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('147', 'admin', '2', 'Admin/AuthManager/modelauth', '模型授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('148', 'admin', '2', 'Admin/AuthManager/addToModel', '保存模型授权', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('149', 'admin', '2', 'Admin/Addons/create', '创建', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('150', 'admin', '2', 'Admin/Addons/checkForm', '检测创建', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('151', 'admin', '2', 'Admin/Addons/preview', '预览', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('152', 'admin', '2', 'Admin/Addons/build', '快速生成插件', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('153', 'admin', '2', 'Admin/Addons/config', '设置', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('154', 'admin', '2', 'Admin/Addons/disable', '禁用', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('155', 'admin', '2', 'Admin/Addons/enable', '启用', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('156', 'admin', '2', 'Admin/Addons/install', '安装', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('157', 'admin', '2', 'Admin/Addons/uninstall', '卸载', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('158', 'admin', '2', 'Admin/Addons/saveconfig', '更新配置', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('159', 'admin', '2', 'Admin/Addons/adminList', '插件后台列表', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('160', 'admin', '2', 'Admin/Addons/execute', 'URL方式访问插件', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('161', 'admin', '2', 'Admin/Addons/hooks', '钩子管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('162', 'admin', '2', 'Admin/Model/index', '模型管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('163', 'admin', '2', 'Admin/model/add', '新增', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('164', 'admin', '2', 'Admin/model/edit', '编辑', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('165', 'admin', '2', 'Admin/model/setStatus', '改变状态', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('166', 'admin', '2', 'Admin/model/update', '保存数据', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('167', 'admin', '2', 'Admin/Attribute/index', '属性管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('168', 'admin', '2', 'Admin/Attribute/add', '新增', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('169', 'admin', '2', 'Admin/Attribute/edit', '编辑', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('170', 'admin', '2', 'Admin/Attribute/setStatus', '改变状态', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('171', 'admin', '2', 'Admin/Attribute/update', '保存数据', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('172', 'admin', '2', 'Admin/Config/index', '配置管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('173', 'admin', '2', 'Admin/Config/edit', '编辑', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('174', 'admin', '2', 'Admin/Config/del', '删除', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('175', 'admin', '2', 'Admin/Config/add', '新增', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('176', 'admin', '2', 'Admin/Config/save', '保存', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('177', 'admin', '2', 'Admin/Menu/index', '菜单管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('178', 'admin', '2', 'Admin/Channel/index', '导航管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('179', 'admin', '2', 'Admin/Channel/add', '新增', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('180', 'admin', '2', 'Admin/Channel/edit', '编辑', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('181', 'admin', '2', 'Admin/Channel/del', '删除', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('182', 'admin', '2', 'Admin/Category/index', '分类管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('183', 'admin', '2', 'Admin/Category/edit', '编辑', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('184', 'admin', '2', 'Admin/Category/add', '新增', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('185', 'admin', '2', 'Admin/Category/remove', '删除', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('186', 'admin', '2', 'Admin/Category/move', '移动', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('187', 'admin', '2', 'Admin/Category/merge', '合并', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('188', 'admin', '2', 'Admin/Database/index?type=export', '备份数据库', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('189', 'admin', '2', 'Admin/Database/export', '备份', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('190', 'admin', '2', 'Admin/Database/optimize', '优化表', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('191', 'admin', '2', 'Admin/Database/repair', '修复表', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('192', 'admin', '2', 'Admin/Database/index?type=import', '还原数据库', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('193', 'admin', '2', 'Admin/Database/import', '恢复', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('194', 'admin', '2', 'Admin/Database/del', '删除', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('195', 'admin', '2', 'Admin/other', '其他', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('196', 'admin', '2', 'Admin/Menu/add', '新增', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('197', 'admin', '2', 'Admin/Menu/edit', '编辑', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('198', 'admin', '2', 'Admin/Think/lists?model=article', '应用', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('199', 'admin', '2', 'Admin/Think/lists?model=download', '下载管理', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('200', 'admin', '2', 'Admin/Think/lists?model=config', '应用', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('201', 'admin', '2', 'Admin/Action/actionlog', '行为日志', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('202', 'admin', '2', 'Admin/User/updatePassword', '修改密码', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('203', 'admin', '2', 'Admin/User/updateNickname', '修改昵称', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('204', 'admin', '2', 'Admin/action/edit', '查看行为日志', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('206', 'admin', '1', 'Admin/think/edit', '编辑数据', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('207', 'admin', '1', 'Admin/Menu/import', '导入', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('208', 'admin', '1', 'Admin/Model/generate', '生成', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('209', 'admin', '1', 'Admin/Addons/addHook', '新增钩子', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('210', 'admin', '1', 'Admin/Addons/edithook', '编辑钩子', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('211', 'admin', '1', 'Admin/Article/sort', '文档排序', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('212', 'admin', '1', 'Admin/Config/sort', '排序', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('213', 'admin', '1', 'Admin/Menu/sort', '排序', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('214', 'admin', '1', 'Admin/Channel/sort', '排序', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('215', 'admin', '1', 'Admin/Category/operate/type/move', '移动', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('216', 'admin', '1', 'Admin/Category/operate/type/merge', '合并', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('217', 'admin', '1', 'Admin/Docapi/Opapi/catLists', 'API分类列表', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('218', 'admin', '1', 'Admin/Docapi/Opapi/catEdit', 'API分类查看编辑', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('219', 'admin', '1', 'Admin/Docapi/Opapi/catSave', 'API分类编辑保存', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('220', 'admin', '1', 'Admin/Docapi/Opapi/catAdd', 'API分类编辑添加', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('221', 'admin', '1', 'Admin/Docapi/Opapi/lists', 'API接口列表', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('222', 'admin', '1', 'Admin/Docapi/Opapi/view', 'API接口展示', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('223', 'admin', '1', 'Admin/Docapi/Opapi/edit', 'API接口调试', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('224', 'admin', '1', 'Admin/Docapi/Opapi/add', 'API接口添加', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('225', 'admin', '1', 'Admin/Docapi/Opapi/save', 'API接口保存', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('226', 'admin', '2', 'Admin//', '前台功能', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('227', 'admin', '1', 'Admin//Docapi/index', 'API接口首页', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('228', 'admin', '2', 'Admin/Docapi', '前台功能', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('229', 'admin', '1', 'Admin/Docapi/index/index', 'API接口首页', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('230', 'admin', '1', 'Admin/Docapi/index', 'API接口首页', '-1', '');
INSERT INTO `apisys_auth_rule` VALUES ('231', 'admin', '1', 'Admin/Docapi/opapi/index', 'API接口首页着路页', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('232', 'admin', '1', 'Admin/Docapi/Opapi/outWord', 'API接口导出word', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('233', 'admin', '2', 'Admin/Docapi/', 'API接口（不能删）', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('234', 'admin', '1', 'Admin/Docapi/Opapi/del', 'API接口删除', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('235', 'admin', '1', 'Admin/Opapi/cache_catlist', '刷新缓存', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('236', 'admin', '2', 'Admin/{:U(\'Docapi/index\')}', '前台访问', '1', '');
INSERT INTO `apisys_auth_rule` VALUES ('237', 'admin', '1', 'Admin/Docapi/Opapi/edit/id', 'API接口保存', '1', '');

-- ----------------------------
-- Table structure for apisys_category
-- ----------------------------
DROP TABLE IF EXISTS `apisys_category`;
CREATE TABLE `apisys_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `name` varchar(30) NOT NULL COMMENT '标志',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `list_row` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '列表每页行数',
  `meta_title` varchar(50) NOT NULL DEFAULT '' COMMENT 'SEO的网页标题',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `template_index` varchar(100) NOT NULL COMMENT '频道页模板',
  `template_lists` varchar(100) NOT NULL COMMENT '列表页模板',
  `template_detail` varchar(100) NOT NULL COMMENT '详情页模板',
  `template_edit` varchar(100) NOT NULL COMMENT '编辑页模板',
  `model` varchar(100) NOT NULL DEFAULT '' COMMENT '关联模型',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '允许发布的内容类型',
  `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '外链',
  `allow_publish` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许发布内容',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '可见性',
  `reply` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许回复',
  `check` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发布的文章是否需要审核',
  `reply_model` varchar(100) NOT NULL DEFAULT '',
  `extend` text NOT NULL COMMENT '扩展设置',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  `icon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类图标',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='分类表';

-- ----------------------------
-- Records of apisys_category
-- ----------------------------
INSERT INTO `apisys_category` VALUES ('43', 'help', '系统帮助', '0', '0', '10', '', '', '', '', '', '', '', '2', '2,1,3', '0', '1', '1', '1', '0', '', '', '1418990655', '1418979598', '1', '0');
INSERT INTO `apisys_category` VALUES ('44', '1shiyong', '关于系统', '43', '0', '10', '', '', '', '', '', '', '', '2', '2,1,3', '0', '1', '1', '1', '0', '', '', '1418930816', '1418959543', '1', '0');

-- ----------------------------
-- Table structure for apisys_categoryapi
-- ----------------------------
DROP TABLE IF EXISTS `apisys_categoryapi`;
CREATE TABLE `apisys_categoryapi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) NOT NULL COMMENT '项目分类',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `son_ids` varchar(255) NOT NULL COMMENT '子集合',
  `category_memo` text NOT NULL COMMENT '分类备注',
  `category_url` varchar(255) NOT NULL COMMENT '分类URL',
  `status` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of apisys_categoryapi
-- ----------------------------
INSERT INTO `apisys_categoryapi` VALUES ('14', '无人机项目APP接口', '0', '', '无人机项目', 'http://apisystem.cn/wurenji', '1');
INSERT INTO `apisys_categoryapi` VALUES ('15', '方向控制', '14', '', '方向控制', 'controlDirection', '1');

-- ----------------------------
-- Table structure for apisys_channel
-- ----------------------------
DROP TABLE IF EXISTS `apisys_channel`;
CREATE TABLE `apisys_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '频道ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级频道ID',
  `title` char(30) NOT NULL COMMENT '频道标题',
  `url` char(100) NOT NULL COMMENT '频道连接',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `target` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '新窗口打开',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_channel
-- ----------------------------
INSERT INTO `apisys_channel` VALUES ('1', '0', '首页', 'Index/index', '1', '1418975111', '1418923177', '1', '0');
INSERT INTO `apisys_channel` VALUES ('4', '0', 'API文档管理', '/docapi/index', '5', '1418946225', '1418966591', '1', '0');
INSERT INTO `apisys_channel` VALUES ('5', '0', '开发帮助', '/Home/article/index/category/help', '2', '1418926220', '1418966602', '1', '0');
INSERT INTO `apisys_channel` VALUES ('6', '0', 'POST & GET测试工具', '/Home/index/toolsTestApi/', '6', '1418966718', '1418967810', '1', '0');
INSERT INTO `apisys_channel` VALUES ('7', '0', '交流论坛', 'http://bbs.apisystem.cn/', '7', '1418966814', '1418966814', '1', '0');

-- ----------------------------
-- Table structure for apisys_config
-- ----------------------------
DROP TABLE IF EXISTS `apisys_config`;
CREATE TABLE `apisys_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_config
-- ----------------------------
INSERT INTO `apisys_config` VALUES ('1', 'WEB_SITE_TITLE', '1', '网站标题', '1', '', '网站标题前台显示标题', '1418998976', '1418935274', '1', 'ApiSystem文档管理系统', '0');
INSERT INTO `apisys_config` VALUES ('2', 'WEB_SITE_DESCRIPTION', '2', '网站描述', '1', '', '网站搜索引擎描述', '1418998976', '1418935841', '1', 'ApiSystem文档管理系统', '1');
INSERT INTO `apisys_config` VALUES ('3', 'WEB_SITE_KEYWORD', '2', '网站关键字', '1', '', '网站搜索引擎关键字', '1418998976', '1418990100', '1', 'ApiSystem，接口文档，管理系统', '8');
INSERT INTO `apisys_config` VALUES ('4', 'WEB_SITE_CLOSE', '4', '关闭站点', '1', '0:关闭,1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', '1418998976', '1418935296', '1', '1', '1');
INSERT INTO `apisys_config` VALUES ('9', 'CONFIG_TYPE_LIST', '3', '配置类型列表', '4', '', '主要用于数据解析和页面表单的生成', '1418998976', '1418935348', '1', '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举', '2');
INSERT INTO `apisys_config` VALUES ('10', 'WEB_SITE_ICP', '1', '网站备案号', '1', '', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2', '1418900335', '1418935859', '1', '', '9');
INSERT INTO `apisys_config` VALUES ('11', 'DOCUMENT_POSITION', '3', '文档推荐位', '2', '', '文档推荐位，推荐到多个位置KEY值相加即可', '1418953380', '1418935329', '1', '1:列表页推荐\r\n2:频道页推荐\r\n4:网站首页推荐', '3');
INSERT INTO `apisys_config` VALUES ('12', 'DOCUMENT_DISPLAY', '3', '文档可见性', '2', '', '文章可见性仅影响前台显示，后台不收影响', '1418956370', '1418935322', '1', '0:所有人可见\r\n1:仅注册会员可见\r\n2:仅管理员可见', '4');
INSERT INTO `apisys_config` VALUES ('13', 'COLOR_STYLE', '4', '后台色系', '1', 'default_color:默认\r\nblue_color:紫罗兰', '后台颜色风格', '1418922533', '1418935904', '1', 'blue_color', '10');
INSERT INTO `apisys_config` VALUES ('20', 'CONFIG_GROUP_LIST', '3', '配置分组', '4', '', '配置分组', '1418928036', '1418918383', '1', '1:基本\r\n2:内容\r\n3:用户\r\n4:系统', '4');
INSERT INTO `apisys_config` VALUES ('21', 'HOOKS_TYPE', '3', '钩子的类型', '4', '', '类型 1-用于扩展显示内容，2-用于扩展业务处理', '1418913397', '1418913407', '1', '1:视图\r\n2:控制器', '6');
INSERT INTO `apisys_config` VALUES ('22', 'AUTH_CONFIG', '3', 'Auth配置', '4', '', '自定义Auth.class.php类配置', '1418909310', '1418909564', '1', 'AUTH_ON:1\r\nAUTH_TYPE:2', '8');
INSERT INTO `apisys_config` VALUES ('23', 'OPEN_DRAFTBOX', '4', '是否开启草稿功能', '2', '0:关闭草稿功能\r\n1:开启草稿功能\r\n', '新增文章时的草稿功能配置', '1418984332', '1418984591', '1', '1', '1');
INSERT INTO `apisys_config` VALUES ('24', 'DRAFT_AOTOSAVE_INTERVAL', '0', '自动保存草稿时间', '2', '', '自动保存草稿的时间间隔，单位：秒', '1418984574', '1418943323', '1', '60', '2');
INSERT INTO `apisys_config` VALUES ('25', 'LIST_ROWS', '0', '后台每页记录数', '2', '', '后台数据每页显示记录数', '1418903896', '1418927745', '1', '10', '10');
INSERT INTO `apisys_config` VALUES ('26', 'USER_ALLOW_REGISTER', '4', '是否允许用户注册', '3', '0:关闭注册\r\n1:允许注册', '是否开放用户注册', '1418904487', '1418904580', '1', '1', '3');
INSERT INTO `apisys_config` VALUES ('27', 'CODEMIRROR_THEME', '4', '预览插件的CodeMirror主题', '4', '3024-day:3024 day\r\n3024-night:3024 night\r\nambiance:ambiance\r\nbase16-dark:base16 dark\r\nbase16-light:base16 light\r\nblackboard:blackboard\r\ncobalt:cobalt\r\neclipse:eclipse\r\nelegant:elegant\r\nerlang-dark:erlang-dark\r\nlesser-dark:lesser-dark\r\nmidnight:midnight', '详情见CodeMirror官网', '1418914385', '1418940813', '1', 'ambiance', '3');
INSERT INTO `apisys_config` VALUES ('28', 'DATA_BACKUP_PATH', '1', '数据库备份根路径', '4', '', '路径必须以 / 结尾', '1418982411', '1418982411', '1', './Data/', '5');
INSERT INTO `apisys_config` VALUES ('29', 'DATA_BACKUP_PART_SIZE', '0', '数据库备份卷大小', '4', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '1418982488', '1418929564', '1', '14189520', '7');
INSERT INTO `apisys_config` VALUES ('30', 'DATA_BACKUP_COMPRESS', '4', '数据库备份文件是否启用压缩', '4', '0:不压缩\r\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', '1418913345', '1418929544', '1', '1', '9');
INSERT INTO `apisys_config` VALUES ('31', 'DATA_BACKUP_COMPRESS_LEVEL', '4', '数据库备份文件压缩级别', '4', '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', '1418913408', '1418913408', '1', '9', '10');
INSERT INTO `apisys_config` VALUES ('32', 'DEVELOP_MODE', '4', '开启开发者模式', '4', '0:关闭\r\n1:开启', '是否开启开发者模式', '1418905995', '1418991877', '1', '1', '11');
INSERT INTO `apisys_config` VALUES ('33', 'ALLOW_VISIT', '3', '不受限控制器方法', '0', '', '', '1418944047', '1418944741', '1', '0:article/draftbox\r\n1:article/mydocument\r\n2:Category/tree\r\n3:Index/verify\r\n4:file/upload\r\n5:file/download\r\n6:user/updatePassword\r\n7:user/updateNickname\r\n8:user/submitPassword\r\n9:user/submitNickname\r\n10:file/uploadpicture', '0');
INSERT INTO `apisys_config` VALUES ('34', 'DENY_VISIT', '3', '超管专限控制器方法', '0', '', '仅超级管理员可访问的控制器方法', '1418944141', '1418944659', '1', '0:Addons/addhook\r\n1:Addons/edithook\r\n2:Addons/delhook\r\n3:Addons/updateHook\r\n4:Admin/getMenus\r\n5:Admin/recordList\r\n6:AuthManager/updateRules\r\n7:AuthManager/tree', '0');
INSERT INTO `apisys_config` VALUES ('35', 'REPLY_LIST_ROWS', '0', '回复列表每页条数', '2', '', '', '1418945376', '1418978083', '1', '10', '0');
INSERT INTO `apisys_config` VALUES ('36', 'ADMIN_ALLOW_IP', '2', '后台允许访问IP', '4', '', '多个用逗号分隔，如果不配置表示不限制IP访问', '1418965454', '1418965553', '1', '', '12');
INSERT INTO `apisys_config` VALUES ('37', 'SHOW_PAGE_TRACE', '4', '是否显示页面Trace', '4', '0:关闭\r\n1:开启', '是否显示页面Trace信息', '1418965685', '1418965685', '1', '0', '1');
INSERT INTO `apisys_config` VALUES ('38', 'APISYS_VERSION', '2', 'API文档系统版本', '4', '', '', '1418920741', '1418920852', '1', '2.3.0118', '0');

-- ----------------------------
-- Table structure for apisys_docapi
-- ----------------------------
DROP TABLE IF EXISTS `apisys_docapi`;
CREATE TABLE `apisys_docapi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(50) NOT NULL COMMENT '接口名称',
  `description` varchar(255) NOT NULL COMMENT '接口描述',
  `class_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '接口分类',
  `method` char(50) NOT NULL DEFAULT 'GET' COMMENT 'method',
  `apiurl` varchar(255) NOT NULL COMMENT '接口地址',
  `request` text NOT NULL COMMENT '请求字段',
  `restring` text NOT NULL COMMENT '返回参数',
  `memo` text NOT NULL COMMENT '备注',
  `keywords` varchar(50) NOT NULL COMMENT '关键词',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目分类',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `edit_time` int(10) NOT NULL COMMENT '编辑时间',
  `user_id` int(10) unsigned NOT NULL COMMENT '创建者',
  `status` char(50) NOT NULL COMMENT '状态',
  `response` text NOT NULL COMMENT '返回字段',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of apisys_docapi
-- ----------------------------
INSERT INTO `apisys_docapi` VALUES ('28', '平衡飞行控制', '是控制器保持平衡', '0', 'POST', 'getBalance', 'a:5:{i:0;a:5:{s:8:\"key_name\";s:5:\"appId\";s:8:\"key_type\";s:6:\"string\";s:8:\"key_must\";s:1:\"1\";s:15:\"key_description\";s:0:\"\";s:9:\"key_value\";s:4:\"2231\";}i:1;a:5:{s:8:\"key_name\";s:8:\"Latitude\";s:8:\"key_type\";s:5:\"float\";s:8:\"key_must\";s:1:\"1\";s:15:\"key_description\";s:6:\"经度\";s:9:\"key_value\";s:9:\"104.48060\";}i:2;a:5:{s:8:\"key_name\";s:9:\"Longitude\";s:8:\"key_type\";s:5:\"float\";s:8:\"key_must\";s:1:\"1\";s:15:\"key_description\";s:6:\"纬度\";s:9:\"key_value\";s:8:\"36.30556\";}i:3;a:5:{s:8:\"key_name\";s:6:\"Height\";s:8:\"key_type\";s:6:\"string\";s:8:\"key_must\";s:1:\"1\";s:15:\"key_description\";s:6:\"高度\";s:9:\"key_value\";s:5:\"2.346\";}i:4;a:5:{s:8:\"key_name\";s:0:\"\";s:8:\"key_type\";s:6:\"string\";s:8:\"key_must\";s:1:\"1\";s:15:\"key_description\";s:0:\"\";s:9:\"key_value\";s:0:\"\";}}', '{\r\n    \"flag\":\"1\",\r\n    \"msg_code\":\"0x00\",\r\n    \"msg\":\"调用接口成功\",\r\n    \"time\":\"1262281163\",\r\n    \"session\":\"655cd513e0abd531c5a6a230370b\",\r\n    \"info\":{\r\n        \"Latitude\":\"104.48060\",\r\n        \"Longitude\":\"36.30556\",\r\n        \"Height\":\"2.346\"\r\n    }\r\n}', '', '', '15', '1418981160', '1418933455', '3', '1', 'a:5:{i:0;a:3:{s:8:\"key_name\";s:4:\"flag\";s:8:\"key_type\";s:6:\"string\";s:15:\"key_description\";s:37:\"标志位1为正常  0为返回失败\";}i:1;a:3:{s:8:\"key_name\";s:8:\"msg_code\";s:8:\"key_type\";s:6:\"string\";s:15:\"key_description\";s:12:\"错误编码\";}i:2;a:3:{s:8:\"key_name\";s:3:\"msg\";s:8:\"key_type\";s:6:\"string\";s:15:\"key_description\";s:12:\"错误消息\";}i:3;a:3:{s:8:\"key_name\";s:4:\"time\";s:8:\"key_type\";s:6:\"string\";s:15:\"key_description\";s:18:\"接口返回时间\";}i:4;a:3:{s:8:\"key_name\";s:0:\"\";s:8:\"key_type\";s:6:\"string\";s:15:\"key_description\";s:0:\"\";}}');
INSERT INTO `apisys_docapi` VALUES ('31', '登录', '', '0', 'GET', '', 'a:1:{i:0;a:5:{s:8:\"key_name\";s:5:\"dfds \";s:8:\"key_type\";s:6:\"string\";s:8:\"key_must\";s:1:\"1\";s:15:\"key_description\";s:0:\"\";s:9:\"key_value\";s:2:\"11\";}}', '', '', '', '15', '1418909466', '1418909466', '1', '0', 'a:0:{}');

-- ----------------------------
-- Table structure for apisys_document
-- ----------------------------
DROP TABLE IF EXISTS `apisys_document`;
CREATE TABLE `apisys_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `name` char(40) NOT NULL DEFAULT '' COMMENT '标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '标题',
  `category_id` int(10) unsigned NOT NULL COMMENT '所属分类',
  `description` char(140) NOT NULL DEFAULT '' COMMENT '描述',
  `root` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '根节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属ID',
  `model_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '内容类型',
  `position` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '推荐位',
  `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '外链',
  `cover_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '封面',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可见性',
  `deadline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截至时间',
  `attach` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件数量',
  `view` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `comment` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `extend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '扩展统计字段',
  `level` int(10) NOT NULL DEFAULT '0' COMMENT '优先级',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  PRIMARY KEY (`id`),
  KEY `idx_category_status` (`category_id`,`status`),
  KEY `idx_status_type_pid` (`status`,`uid`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='文档模型基础表';

-- ----------------------------
-- Records of apisys_document
-- ----------------------------
INSERT INTO `apisys_document` VALUES ('4', '1', '', 'API 设计清单', '43', '', '0', '0', '2', '2', '7', '0', '0', '1', '0', '0', '0', '0', '0', '0', '1418926040', '1418930936', '-1');
INSERT INTO `apisys_document` VALUES ('5', '1', '', '说明帮助', '44', '', '0', '0', '2', '2', '7', '0', '0', '1', '0', '0', '3', '0', '0', '0', '1418930880', '1490770313', '1');

-- ----------------------------
-- Table structure for apisys_document_apidoc
-- ----------------------------
DROP TABLE IF EXISTS `apisys_document_apidoc`;
CREATE TABLE `apisys_document_apidoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `method` char(50) NOT NULL DEFAULT 'GET' COMMENT 'method',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of apisys_document_apidoc
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_document_article
-- ----------------------------
DROP TABLE IF EXISTS `apisys_document_article`;
CREATE TABLE `apisys_document_article` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `parse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容解析类型',
  `content` text NOT NULL COMMENT '文章内容',
  `template` varchar(100) NOT NULL DEFAULT '' COMMENT '详情页显示模板',
  `bookmark` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型文章表';

-- ----------------------------
-- Records of apisys_document_article
-- ----------------------------
INSERT INTO `apisys_document_article` VALUES ('5', '0', '<div align=\"left\">\r\n	<p>\r\n		<br />\r\n	</p>\r\n	<h4>\r\n		在线帮助文档\r\n	</h4>\r\n	<h4>\r\n		<a href=\"http://bbs.apisystem.cn/forum.php?mod=viewthread&tid=1&extra=page%3D1\" target=\"_blank\">http://bbs.apisystem.cn/forum.php?mod=viewthread&amp;tid=1&amp;extra=page%3D1</a> \r\n	</h4>\r\n	<p>\r\n		<br />\r\n	</p>\r\n	<p>\r\n		<strong>ApiSystem是什么？解决什么问题？</strong> \r\n	</p>\r\n</div>\r\n<div align=\"left\">\r\n	APISystem文档管理系统是一个开源API接口文档管理系统，\r\nApiSystem将原来用word编写API文档流程中解放出来，只需要按照填写文本框即可生成接口文档，管理文档也很轻松，同时还可以配置可见及所得的调试工具，API接口也可以一键导出word文档让你既可以在线分权限分享也可线下分享，是中小企业IT团队开发的福音。\r\n</div>\r\n<div align=\"left\">\r\n	ApiSystem接口管理系统开发与2004年，经过几次迭代2005年形成一个稳定形态与大家见面。\r\n</div>\r\n<div align=\"left\">\r\n	ApiSystem基于ThinkPHP3.2和OneThink开发，简单实用，希望让开发更加快捷高效。\r\n</div>\r\n<div align=\"left\">\r\n	ApiSystem遵循Apache2开源协议发布，并提供免费使用。\r\n</div>\r\n<br />\r\n<div align=\"left\">\r\n	<p>\r\n		官网 <a href=\"http://www.apisystem.cn\" target=\"_blank\" class=\"gj_safe_a\">http://www.apisystem.cn</a> \r\n	</p>\r\n	<p>\r\n		交流论坛 <a target=\"_blank\" class=\"gj_safe_a\" href=\"http://bbs.apisystem.cn\">http://bbs.apisystem.cn</a> \r\n	</p>\r\n</div>\r\n<div align=\"left\">\r\n	交流QQ群 577693968 交流QQ群2 460098419\r\n</div>\r\n<div align=\"left\">\r\n	Author: Texren&nbsp;&nbsp;QQ: 174463651\r\n</div>\r\n<div align=\"left\">\r\n	<p>\r\n		&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Smith77 QQ: 3246932472\r\n	</p>\r\n	<p>\r\n		<br />\r\n	</p>\r\n	<p>\r\n		<strong>感谢</strong> \r\n	</p>\r\n	<div align=\"left\">\r\n		<p>\r\n			感谢Texren和Smith77的多年不懈努力，使得这个这个版本能够最终发布\r\n		</p>\r\n	</div>\r\n	<div align=\"left\">\r\n		感谢ThinkPHP提供优秀国产php开源框架\r\n	</div>\r\n	<div align=\"left\">\r\n		<p>\r\n			感谢Onethink提供开源Tp demo系统\r\n		</p>\r\n	</div>\r\n	<p>\r\n		<br />\r\n	</p>\r\n</div>\r\n<br />', '', '0');

-- ----------------------------
-- Table structure for apisys_document_download
-- ----------------------------
DROP TABLE IF EXISTS `apisys_document_download`;
CREATE TABLE `apisys_document_download` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `parse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容解析类型',
  `content` text NOT NULL COMMENT '下载详细描述',
  `template` varchar(100) NOT NULL DEFAULT '' COMMENT '详情页显示模板',
  `file_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型下载表';

-- ----------------------------
-- Records of apisys_document_download
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_file
-- ----------------------------
DROP TABLE IF EXISTS `apisys_file`;
CREATE TABLE `apisys_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` char(20) NOT NULL DEFAULT '' COMMENT '保存名称',
  `savepath` char(30) NOT NULL DEFAULT '' COMMENT '文件保存路径',
  `ext` char(5) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `location` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件保存位置',
  `create_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_md5` (`md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件表';

-- ----------------------------
-- Records of apisys_file
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_hooks
-- ----------------------------
DROP TABLE IF EXISTS `apisys_hooks`;
CREATE TABLE `apisys_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addons` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子挂载的插件 ''，''分割',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_hooks
-- ----------------------------
INSERT INTO `apisys_hooks` VALUES ('1', 'pageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', '1', '0', '');
INSERT INTO `apisys_hooks` VALUES ('2', 'pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', '1', '0', 'ReturnTop');
INSERT INTO `apisys_hooks` VALUES ('3', 'documentEditForm', '添加编辑表单的 扩展内容钩子', '1', '0', 'Attachment');
INSERT INTO `apisys_hooks` VALUES ('4', 'documentDetailAfter', '文档末尾显示', '1', '0', 'Attachment,SocialComment');
INSERT INTO `apisys_hooks` VALUES ('5', 'documentDetailBefore', '页面内容前显示用钩子', '1', '0', '');
INSERT INTO `apisys_hooks` VALUES ('6', 'documentSaveComplete', '保存文档数据后的扩展钩子', '2', '0', 'Attachment');
INSERT INTO `apisys_hooks` VALUES ('7', 'documentEditFormContent', '添加编辑表单的内容显示钩子', '1', '0', 'Editor');
INSERT INTO `apisys_hooks` VALUES ('8', 'adminArticleEdit', '后台内容编辑页编辑器', '1', '1418982734', 'EditorForAdmin');
INSERT INTO `apisys_hooks` VALUES ('13', 'AdminIndex', '首页小格子个性化显示', '1', '1418996073', 'SiteStat,SystemInfo,DevTeam');
INSERT INTO `apisys_hooks` VALUES ('14', 'topicComment', '评论提交方式扩展钩子。', '1', '1418963518', 'Editor');
INSERT INTO `apisys_hooks` VALUES ('16', 'app_begin', '应用开始', '2', '1418981614', '');

-- ----------------------------
-- Table structure for apisys_member
-- ----------------------------
DROP TABLE IF EXISTS `apisys_member`;
CREATE TABLE `apisys_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  PRIMARY KEY (`uid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
-- Records of apisys_member
-- ----------------------------
INSERT INTO `apisys_member` VALUES ('1', 'admin', '0', '0000-00-00', '', '510', '104', '0', '1418921698', '2130706433', '1490770172', '1');
INSERT INTO `apisys_member` VALUES ('2', 'apimanager', '0', '0000-00-00', '', '0', '0', '0', '0', '0', '0', '1');
INSERT INTO `apisys_member` VALUES ('3', 'apiview', '0', '0000-00-00', '', '20', '7', '0', '0', '1418906433', '1418955508', '1');

-- ----------------------------
-- Table structure for apisys_menu
-- ----------------------------
DROP TABLE IF EXISTS `apisys_menu`;
CREATE TABLE `apisys_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_menu
-- ----------------------------
INSERT INTO `apisys_menu` VALUES ('1', '首页', '0', '1', 'Index/index', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('2', '内容', '0', '2', 'Article/mydocument', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('3', '文档列表', '2', '0', 'article/index', '1', '', '内容', '0');
INSERT INTO `apisys_menu` VALUES ('4', '新增', '3', '0', 'article/add', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('5', '编辑', '3', '0', 'article/edit', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('6', '改变状态', '3', '0', 'article/setStatus', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('7', '保存', '3', '0', 'article/update', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('8', '保存草稿', '3', '0', 'article/autoSave', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('9', '移动', '3', '0', 'article/move', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('10', '复制', '3', '0', 'article/copy', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('11', '粘贴', '3', '0', 'article/paste', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('12', '导入', '3', '0', 'article/batchOperate', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('13', '回收站', '2', '0', 'article/recycle', '1', '', '内容', '0');
INSERT INTO `apisys_menu` VALUES ('14', '还原', '13', '0', 'article/permit', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('15', '清空', '13', '0', 'article/clear', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('16', '用户', '0', '3', 'User/index', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('17', '用户信息', '16', '0', 'User/index', '0', '', '用户管理', '0');
INSERT INTO `apisys_menu` VALUES ('18', '新增用户', '17', '0', 'User/add', '0', '添加新用户', '', '0');
INSERT INTO `apisys_menu` VALUES ('19', '用户行为', '16', '0', 'User/action', '0', '', '行为管理', '0');
INSERT INTO `apisys_menu` VALUES ('20', '新增用户行为', '19', '0', 'User/addaction', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('21', '编辑用户行为', '19', '0', 'User/editaction', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('22', '保存用户行为', '19', '0', 'User/saveAction', '0', '\"用户->用户行为\"保存编辑和新增的用户行为', '', '0');
INSERT INTO `apisys_menu` VALUES ('23', '变更行为状态', '19', '0', 'User/setStatus', '0', '\"用户->用户行为\"中的启用,禁用和删除权限', '', '0');
INSERT INTO `apisys_menu` VALUES ('24', '禁用会员', '19', '0', 'User/changeStatus?method=forbidUser', '0', '\"用户->用户信息\"中的禁用', '', '0');
INSERT INTO `apisys_menu` VALUES ('25', '启用会员', '19', '0', 'User/changeStatus?method=resumeUser', '0', '\"用户->用户信息\"中的启用', '', '0');
INSERT INTO `apisys_menu` VALUES ('26', '删除会员', '19', '0', 'User/changeStatus?method=deleteUser', '0', '\"用户->用户信息\"中的删除', '', '0');
INSERT INTO `apisys_menu` VALUES ('27', '权限管理', '16', '0', 'AuthManager/index', '0', '', '用户管理', '0');
INSERT INTO `apisys_menu` VALUES ('28', '删除', '27', '0', 'AuthManager/changeStatus?method=deleteGroup', '0', '删除用户组', '', '0');
INSERT INTO `apisys_menu` VALUES ('29', '禁用', '27', '0', 'AuthManager/changeStatus?method=forbidGroup', '0', '禁用用户组', '', '0');
INSERT INTO `apisys_menu` VALUES ('30', '恢复', '27', '0', 'AuthManager/changeStatus?method=resumeGroup', '0', '恢复已禁用的用户组', '', '0');
INSERT INTO `apisys_menu` VALUES ('31', '新增', '27', '0', 'AuthManager/createGroup', '0', '创建新的用户组', '', '0');
INSERT INTO `apisys_menu` VALUES ('32', '编辑', '27', '0', 'AuthManager/editGroup', '0', '编辑用户组名称和描述', '', '0');
INSERT INTO `apisys_menu` VALUES ('33', '保存用户组', '27', '0', 'AuthManager/writeGroup', '0', '新增和编辑用户组的\"保存\"按钮', '', '0');
INSERT INTO `apisys_menu` VALUES ('34', '授权', '27', '0', 'AuthManager/group', '0', '\"后台 \\ 用户 \\ 用户信息\"列表页的\"授权\"操作按钮,用于设置用户所属用户组', '', '0');
INSERT INTO `apisys_menu` VALUES ('35', '访问授权', '27', '0', 'AuthManager/access', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"访问授权\"操作按钮', '', '0');
INSERT INTO `apisys_menu` VALUES ('36', '成员授权', '27', '0', 'AuthManager/user', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"成员授权\"操作按钮', '', '0');
INSERT INTO `apisys_menu` VALUES ('37', '解除授权', '27', '0', 'AuthManager/removeFromGroup', '0', '\"成员授权\"列表页内的解除授权操作按钮', '', '0');
INSERT INTO `apisys_menu` VALUES ('38', '保存成员授权', '27', '0', 'AuthManager/addToGroup', '0', '\"用户信息\"列表页\"授权\"时的\"保存\"按钮和\"成员授权\"里右上角的\"添加\"按钮)', '', '0');
INSERT INTO `apisys_menu` VALUES ('39', '分类授权', '27', '0', 'AuthManager/category', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"分类授权\"操作按钮', '', '0');
INSERT INTO `apisys_menu` VALUES ('40', '保存分类授权', '27', '0', 'AuthManager/addToCategory', '0', '\"分类授权\"页面的\"保存\"按钮', '', '0');
INSERT INTO `apisys_menu` VALUES ('41', '模型授权', '27', '0', 'AuthManager/modelauth', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"模型授权\"操作按钮', '', '0');
INSERT INTO `apisys_menu` VALUES ('42', '保存模型授权', '27', '0', 'AuthManager/addToModel', '0', '\"分类授权\"页面的\"保存\"按钮', '', '0');
INSERT INTO `apisys_menu` VALUES ('43', '扩展', '0', '7', 'Addons/index', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('44', '插件管理', '43', '1', 'Addons/index', '0', '', '扩展', '0');
INSERT INTO `apisys_menu` VALUES ('45', '创建', '44', '0', 'Addons/create', '0', '服务器上创建插件结构向导', '', '0');
INSERT INTO `apisys_menu` VALUES ('46', '检测创建', '44', '0', 'Addons/checkForm', '0', '检测插件是否可以创建', '', '0');
INSERT INTO `apisys_menu` VALUES ('47', '预览', '44', '0', 'Addons/preview', '0', '预览插件定义类文件', '', '0');
INSERT INTO `apisys_menu` VALUES ('48', '快速生成插件', '44', '0', 'Addons/build', '0', '开始生成插件结构', '', '0');
INSERT INTO `apisys_menu` VALUES ('49', '设置', '44', '0', 'Addons/config', '0', '设置插件配置', '', '0');
INSERT INTO `apisys_menu` VALUES ('50', '禁用', '44', '0', 'Addons/disable', '0', '禁用插件', '', '0');
INSERT INTO `apisys_menu` VALUES ('51', '启用', '44', '0', 'Addons/enable', '0', '启用插件', '', '0');
INSERT INTO `apisys_menu` VALUES ('52', '安装', '44', '0', 'Addons/install', '0', '安装插件', '', '0');
INSERT INTO `apisys_menu` VALUES ('53', '卸载', '44', '0', 'Addons/uninstall', '0', '卸载插件', '', '0');
INSERT INTO `apisys_menu` VALUES ('54', '更新配置', '44', '0', 'Addons/saveconfig', '0', '更新插件配置处理', '', '0');
INSERT INTO `apisys_menu` VALUES ('55', '插件后台列表', '44', '0', 'Addons/adminList', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('56', 'URL方式访问插件', '44', '0', 'Addons/execute', '0', '控制是否有权限通过url访问插件控制器方法', '', '0');
INSERT INTO `apisys_menu` VALUES ('57', '钩子管理', '43', '2', 'Addons/hooks', '0', '', '扩展', '0');
INSERT INTO `apisys_menu` VALUES ('58', '模型管理', '68', '3', 'Model/index', '0', '', '系统设置', '0');
INSERT INTO `apisys_menu` VALUES ('59', '新增', '58', '0', 'model/add', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('60', '编辑', '58', '0', 'model/edit', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('61', '改变状态', '58', '0', 'model/setStatus', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('62', '保存数据', '58', '0', 'model/update', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('63', '属性管理', '68', '0', 'Attribute/index', '1', '网站属性配置。', '', '0');
INSERT INTO `apisys_menu` VALUES ('64', '新增', '63', '0', 'Attribute/add', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('65', '编辑', '63', '0', 'Attribute/edit', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('66', '改变状态', '63', '0', 'Attribute/setStatus', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('67', '保存数据', '63', '0', 'Attribute/update', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('68', '系统', '0', '4', 'Config/group', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('69', '网站设置', '68', '1', 'Config/group', '0', '', '系统设置', '0');
INSERT INTO `apisys_menu` VALUES ('70', '配置管理', '68', '4', 'Config/index', '0', '', '系统设置', '0');
INSERT INTO `apisys_menu` VALUES ('71', '编辑', '70', '0', 'Config/edit', '0', '新增编辑和保存配置', '', '0');
INSERT INTO `apisys_menu` VALUES ('72', '删除', '70', '0', 'Config/del', '0', '删除配置', '', '0');
INSERT INTO `apisys_menu` VALUES ('73', '新增', '70', '0', 'Config/add', '0', '新增配置', '', '0');
INSERT INTO `apisys_menu` VALUES ('74', '保存', '70', '0', 'Config/save', '0', '保存配置', '', '0');
INSERT INTO `apisys_menu` VALUES ('75', '菜单管理', '68', '5', 'Menu/index', '0', '', '系统设置', '0');
INSERT INTO `apisys_menu` VALUES ('76', '导航管理', '68', '6', 'Channel/index', '0', '', '系统设置', '0');
INSERT INTO `apisys_menu` VALUES ('77', '新增', '76', '0', 'Channel/add', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('78', '编辑', '76', '0', 'Channel/edit', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('79', '删除', '76', '0', 'Channel/del', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('80', '分类管理', '68', '2', 'Category/index', '0', '', '系统设置', '0');
INSERT INTO `apisys_menu` VALUES ('81', '编辑', '80', '0', 'Category/edit', '0', '编辑和保存栏目分类', '', '0');
INSERT INTO `apisys_menu` VALUES ('82', '新增', '80', '0', 'Category/add', '0', '新增栏目分类', '', '0');
INSERT INTO `apisys_menu` VALUES ('83', '删除', '80', '0', 'Category/remove', '0', '删除栏目分类', '', '0');
INSERT INTO `apisys_menu` VALUES ('84', '移动', '80', '0', 'Category/operate/type/move', '0', '移动栏目分类', '', '0');
INSERT INTO `apisys_menu` VALUES ('85', '合并', '80', '0', 'Category/operate/type/merge', '0', '合并栏目分类', '', '0');
INSERT INTO `apisys_menu` VALUES ('86', '备份数据库', '68', '0', 'Database/index?type=export', '0', '', '数据备份', '0');
INSERT INTO `apisys_menu` VALUES ('87', '备份', '86', '0', 'Database/export', '0', '备份数据库', '', '0');
INSERT INTO `apisys_menu` VALUES ('88', '优化表', '86', '0', 'Database/optimize', '0', '优化数据表', '', '0');
INSERT INTO `apisys_menu` VALUES ('89', '修复表', '86', '0', 'Database/repair', '0', '修复数据表', '', '0');
INSERT INTO `apisys_menu` VALUES ('90', '还原数据库', '68', '0', 'Database/index?type=import', '0', '', '数据备份', '0');
INSERT INTO `apisys_menu` VALUES ('91', '恢复', '90', '0', 'Database/import', '0', '数据库恢复', '', '0');
INSERT INTO `apisys_menu` VALUES ('92', '删除', '90', '0', 'Database/del', '0', '删除备份文件', '', '0');
INSERT INTO `apisys_menu` VALUES ('93', '其他', '0', '5', 'other', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('96', '新增', '75', '0', 'Menu/add', '0', '', '系统设置', '0');
INSERT INTO `apisys_menu` VALUES ('98', '编辑', '75', '0', 'Menu/edit', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('104', '下载管理', '102', '0', 'Think/lists?model=download', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('105', '配置管理', '102', '0', 'Think/lists?model=config', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('106', '行为日志', '16', '0', 'Action/actionlog', '0', '', '行为管理', '0');
INSERT INTO `apisys_menu` VALUES ('108', '修改密码', '16', '0', 'User/updatePassword', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('109', '修改昵称', '16', '0', 'User/updateNickname', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('110', '查看行为日志', '106', '0', 'action/edit', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('112', '新增数据', '58', '0', 'think/add', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('113', '编辑数据', '58', '0', 'think/edit', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('114', '导入', '75', '0', 'Menu/import', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('115', '生成', '58', '0', 'Model/generate', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('116', '新增钩子', '57', '0', 'Addons/addHook', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('117', '编辑钩子', '57', '0', 'Addons/edithook', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('118', '文档排序', '3', '0', 'Article/sort', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('119', '排序', '70', '0', 'Config/sort', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('120', '排序', '75', '0', 'Menu/sort', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('121', '排序', '76', '0', 'Channel/sort', '1', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('123', 'API分类列表', '122', '0', 'Docapi/Opapi/catLists', '0', '', 'API分类列表', '0');
INSERT INTO `apisys_menu` VALUES ('124', 'API分类查看编辑', '123', '0', 'Docapi/Opapi/catEdit', '0', '', 'API分类列表', '0');
INSERT INTO `apisys_menu` VALUES ('125', 'API分类编辑保存', '123', '0', 'Docapi/Opapi/catSave', '0', '', 'API分类列表', '0');
INSERT INTO `apisys_menu` VALUES ('126', 'API分类编辑添加', '123', '0', 'Docapi/Opapi/catAdd', '0', '', 'API分类列表', '0');
INSERT INTO `apisys_menu` VALUES ('127', 'API接口列表', '122', '0', 'Docapi/Opapi/lists', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('128', 'API接口展示', '127', '0', 'Docapi/Opapi/view', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('129', 'API接口编辑', '127', '0', 'Docapi/Opapi/edit', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('130', 'API接口添加', '127', '0', 'Docapi/Opapi/add', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('131', 'API接口保存', '127', '0', 'Docapi/Opapi/edit/id', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('122', 'API接口（不能删）', '0', '99', 'Docapi/', '1', '不能删除否则对前台权限有影响', '', '0');
INSERT INTO `apisys_menu` VALUES ('132', 'API接口首页', '122', '0', 'Docapi/index/index', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('133', 'API接口首页着路页', '122', '0', 'Docapi/opapi/index', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('134', 'API接口导出word', '127', '0', 'Docapi/Opapi/outWord', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('135', 'API接口删除', '127', '0', 'Docapi/Opapi/del', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('137', '刷新缓存', '132', '0', 'Opapi/cache_catlist', '0', '', '', '0');
INSERT INTO `apisys_menu` VALUES ('138', 'API接口调试', '127', '0', 'Docapi/poststr/startRun', '0', '', '', '0');

-- ----------------------------
-- Table structure for apisys_model
-- ----------------------------
DROP TABLE IF EXISTS `apisys_model`;
CREATE TABLE `apisys_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '模型标识',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `extend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '继承的模型',
  `relation` varchar(30) NOT NULL DEFAULT '' COMMENT '继承与被继承模型的关联字段',
  `need_pk` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '新建表时是否需要主键字段',
  `field_sort` text NOT NULL COMMENT '表单字段排序',
  `field_group` varchar(255) NOT NULL DEFAULT '1:基础' COMMENT '字段分组',
  `attribute_list` text NOT NULL COMMENT '属性列表（表的字段）',
  `template_list` varchar(100) NOT NULL DEFAULT '' COMMENT '列表模板',
  `template_add` varchar(100) NOT NULL DEFAULT '' COMMENT '新增模板',
  `template_edit` varchar(100) NOT NULL DEFAULT '' COMMENT '编辑模板',
  `list_grid` text NOT NULL COMMENT '列表定义',
  `list_row` smallint(2) unsigned NOT NULL DEFAULT '10' COMMENT '列表数据长度',
  `search_key` varchar(50) NOT NULL DEFAULT '' COMMENT '默认搜索字段',
  `search_list` varchar(255) NOT NULL DEFAULT '' COMMENT '高级搜索的字段',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `engine_type` varchar(25) NOT NULL DEFAULT 'MyISAM' COMMENT '数据库引擎',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='文档模型表';

-- ----------------------------
-- Records of apisys_model
-- ----------------------------
INSERT INTO `apisys_model` VALUES ('1', 'document', '基础文档', '0', '', '1', '{\"1\":[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\",\"15\",\"16\",\"17\",\"18\",\"19\",\"20\",\"21\",\"22\"]}', '1:基础', '', '', '', '', 'id:编号\r\ntitle:标题:article/index?cate_id=[category_id]&pid=[id]\r\ntype|get_document_type:类型\r\nlevel:优先级\r\nupdate_time|time_format:最后更新\r\nstatus_text:状态\r\nview:浏览\r\nid:操作:[EDIT]&cate_id=[category_id]|编辑,article/setstatus?status=-1&ids=[id]|删除', '0', '', '', '1418991233', '1418907827', '1', 'MyISAM');
INSERT INTO `apisys_model` VALUES ('2', 'article', '文章', '1', '', '1', '{\"1\":[\"3\",\"24\",\"2\",\"5\"],\"2\":[\"9\",\"13\",\"19\",\"10\",\"12\",\"16\",\"17\",\"26\",\"20\",\"14\",\"11\",\"25\"]}', '1:基础,2:扩展', '', '', '', '', 'id:编号\r\ntitle:标题:article/edit?cate_id=[category_id]&id=[id]\r\ncontent:内容', '0', '', '', '1418991243', '1418960622', '1', 'MyISAM');
INSERT INTO `apisys_model` VALUES ('3', 'download', '下载', '1', '', '1', '{\"1\":[\"3\",\"28\",\"30\",\"32\",\"2\",\"5\",\"31\"],\"2\":[\"13\",\"10\",\"27\",\"9\",\"12\",\"16\",\"17\",\"19\",\"11\",\"20\",\"14\",\"29\"]}', '1:基础,2:扩展', '', '', '', '', 'id:编号\r\ntitle:标题', '0', '', '', '1418991252', '1418960449', '0', 'MyISAM');
INSERT INTO `apisys_model` VALUES ('7', 'categoryapi', 'API分类', '0', '', '1', '{\"1\":[\"63\",\"58\",\"62\",\"59\",\"60\",\"61\"]}', '1:基础', '', '', '', '', 'id:编号\r\nname:项目名称:think/edit?model=7&id=[id]\r\ncategory_url:分类URL\r\nparent_id:父ID\r\nson_ids:子集合', '10', '', '', '1418987102', '1418954370', '1', 'MyISAM');
INSERT INTO `apisys_model` VALUES ('6', 'docapi', 'API文档', '0', '', '1', '{\"1\":[\"44\",\"45\",\"52\",\"53\",\"46\",\"47\",\"48\",\"49\",\"64\",\"50\",\"51\",\"54\",\"55\",\"57\"]}', '1:基础', '', '', '', '', 'id:编号\r\ntitle:接口名称:think/edit?model=6&id=[id]\r\nproject_id:项目分类', '10', '', '', '1418925411', '1418929379', '1', 'MyISAM');
INSERT INTO `apisys_model` VALUES ('8', 'apilog', 'API日志', '0', '', '1', '{\"1\":[\"71\",\"70\",\"69\",\"65\",\"67\",\"66\",\"68\"]}', '1:基础', '', '', '', '', 'id:编号\r\nname:项目名称:think/edit?model=8&id=[id]', '10', '', '', '1418937076', '1418953972', '1', 'MyISAM');

-- ----------------------------
-- Table structure for apisys_picture
-- ----------------------------
DROP TABLE IF EXISTS `apisys_picture`;
CREATE TABLE `apisys_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_picture
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_ucenter_admin
-- ----------------------------
DROP TABLE IF EXISTS `apisys_ucenter_admin`;
CREATE TABLE `apisys_ucenter_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员用户ID',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of apisys_ucenter_admin
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_ucenter_app
-- ----------------------------
DROP TABLE IF EXISTS `apisys_ucenter_app`;
CREATE TABLE `apisys_ucenter_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '应用ID',
  `title` varchar(30) NOT NULL COMMENT '应用名称',
  `url` varchar(100) NOT NULL COMMENT '应用URL',
  `ip` char(15) NOT NULL COMMENT '应用IP',
  `auth_key` varchar(100) NOT NULL COMMENT '加密KEY',
  `sys_login` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '同步登陆',
  `allow_ip` varchar(255) NOT NULL COMMENT '允许访问的IP',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '应用状态',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用表';

-- ----------------------------
-- Records of apisys_ucenter_app
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_ucenter_member
-- ----------------------------
DROP TABLE IF EXISTS `apisys_ucenter_member`;
CREATE TABLE `apisys_ucenter_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NOT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL COMMENT '用户手机',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of apisys_ucenter_member
-- ----------------------------
INSERT INTO `apisys_ucenter_member` VALUES ('1', 'admin', 'a57d883c27284750bb22908ce1d69f96', 'admin@apisystem.com', '', '1418921698', '1418906433', '1490770172', '2130706433', '1418921698', '1');
INSERT INTO `apisys_ucenter_member` VALUES ('2', 'apimanager', 'a57d883c27284750bb22908ce1d69f96', 'apimanager@apisystem.cn', '', '1418907499', '1418906433', '0', '0', '1418907499', '1');
INSERT INTO `apisys_ucenter_member` VALUES ('3', 'apiview', 'a57d883c27284750bb22908ce1d69f96', 'apiview@apisystem.cn', '', '1418907561', '1418906433', '1418955508', '1418906433', '1418907561', '1');

-- ----------------------------
-- Table structure for apisys_ucenter_setting
-- ----------------------------
DROP TABLE IF EXISTS `apisys_ucenter_setting`;
CREATE TABLE `apisys_ucenter_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型（1-用户配置）',
  `value` text NOT NULL COMMENT '配置数据',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设置表';

-- ----------------------------
-- Records of apisys_ucenter_setting
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_url
-- ----------------------------
DROP TABLE IF EXISTS `apisys_url`;
CREATE TABLE `apisys_url` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '链接唯一标识',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `short` char(100) NOT NULL DEFAULT '' COMMENT '短网址',
  `status` tinyint(2) NOT NULL DEFAULT '2' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='链接表';

-- ----------------------------
-- Records of apisys_url
-- ----------------------------

-- ----------------------------
-- Table structure for apisys_userdata
-- ----------------------------
DROP TABLE IF EXISTS `apisys_userdata`;
CREATE TABLE `apisys_userdata` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型标识',
  `target_id` int(10) unsigned NOT NULL COMMENT '目标id',
  UNIQUE KEY `uid` (`uid`,`type`,`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apisys_userdata
-- ----------------------------
