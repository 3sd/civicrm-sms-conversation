DROP TABLE IF EXISTS `civicrm_sms_conversation`;
DROP TABLE IF EXISTS `civicrm_sms_conversation_question`;
DROP TABLE IF EXISTS `civicrm_sms_conversation_contact`;
DROP TABLE IF EXISTS `civicrm_sms_conversation_action`;

-- /*******************************************************
-- *
-- * civicrm_sms_conversation_question
-- *
-- * SMS Conversation Question
-- *
-- *******************************************************/
CREATE TABLE `civicrm_sms_conversation_question` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique SmsConversationQuestion ID',
     `text` longtext    COMMENT 'Question Text.',
     `timeout` int unsigned   DEFAULT 0 COMMENT 'Timeout in seconds'
,
        PRIMARY KEY (`id`)



)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_sms_conversation
-- *
-- * SMS Conversation
-- *
-- *******************************************************/
CREATE TABLE `civicrm_sms_conversation` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique SmsConversation ID',
     `is_active` tinyint    COMMENT 'Is this conversation enabled?',
     `start_question_id` int unsigned    COMMENT 'FK to sms_conversation_question.id'
,
        PRIMARY KEY (`id`)


,          CONSTRAINT FK_civicrm_sms_conversation_start_question_id FOREIGN KEY (`start_question_id`) REFERENCES `civicrm_sms_conversation_question`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_sms_conversation_action
-- *
-- * Sms Conversation Action
-- *
-- *******************************************************/
CREATE TABLE `civicrm_sms_conversation_action` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique SmsConversationAction ID',
     `question_id` int unsigned    COMMENT 'FK to sms_conversation_question.id',
     `answer_pattern` varchar(255)    COMMENT 'Regex pattern for answer.',
     `action_type` int unsigned    COMMENT 'FK to sms_conversation_action_type option group',
     `action_data` varchar(0)    COMMENT 'eg. group Id, custom field Id etc',
     `invalid_text` longtext    COMMENT 'Invalid answer Text.'
,
        PRIMARY KEY (`id`)


,          CONSTRAINT FK_civicrm_sms_conversation_action_question_id FOREIGN KEY (`question_id`) REFERENCES `civicrm_sms_conversation_question`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_sms_conversation_contact
-- *
-- * SMS Conversation to contact mapping
-- *
-- *******************************************************/
CREATE TABLE `civicrm_sms_conversation_contact` (


     `conversation_id` int unsigned NOT NULL   COMMENT 'SmsConversation ID',
     `contact_id` int unsigned    COMMENT 'FK to Contact',
     `status_id` int unsigned NOT NULL   COMMENT 'Conversation Status ID',
     `current_question_id` int unsigned    COMMENT 'FK to sms_conversation_question.id',
     `source_contact_id` int unsigned    COMMENT 'Id of contact that started the conversation'


,          CONSTRAINT FK_civicrm_sms_conversation_contact_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_sms_conversation_contact_current_question_id FOREIGN KEY (`current_question_id`) REFERENCES `civicrm_sms_conversation_question`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_sms_conversation_contact_source_contact_id FOREIGN KEY (`source_contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;