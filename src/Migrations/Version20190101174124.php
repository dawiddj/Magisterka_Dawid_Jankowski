<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190101174124 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'oracle', 'Migration can only be executed safely on \'oracle\'.');

        $this->addSql('CREATE SEQUENCE users_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE messages (id NUMBER(10) NOT NULL, created_user_id NUMBER(10) DEFAULT NULL NULL, content VARCHAR2(1024) NOT NULL, created_at TIMESTAMP(0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DECLARE
          constraints_Count NUMBER;
        BEGIN
          SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = \'MESSAGES\' AND CONSTRAINT_TYPE = \'P\';
          IF constraints_Count = 0 OR constraints_Count = \'\' THEN
            EXECUTE IMMEDIATE \'ALTER TABLE MESSAGES ADD CONSTRAINT MESSAGES_AI_PK PRIMARY KEY (ID)\';
          END IF;
        END;');
        $this->addSql('CREATE SEQUENCE MESSAGES_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TRIGGER MESSAGES_AI_PK
           BEFORE INSERT
           ON MESSAGES
           FOR EACH ROW
        DECLARE
           last_Sequence NUMBER;
           last_InsertID NUMBER;
        BEGIN
           SELECT MESSAGES_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
           IF (:NEW.ID IS NULL OR :NEW.ID = 0) THEN
              SELECT MESSAGES_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
           ELSE
              SELECT NVL(Last_Number, 0) INTO last_Sequence
                FROM User_Sequences
               WHERE Sequence_Name = \'MESSAGES_SEQ\';
              SELECT :NEW.ID INTO last_InsertID FROM DUAL;
              WHILE (last_InsertID > last_Sequence) LOOP
                 SELECT MESSAGES_SEQ.NEXTVAL INTO last_Sequence FROM DUAL;
              END LOOP;
           END IF;
        END;');
        $this->addSql('CREATE INDEX IDX_DB021E96E104C1D3 ON messages (created_user_id)');
        $this->addSql('CREATE TABLE tasks (id NUMBER(10) NOT NULL, created_user_id NUMBER(10) DEFAULT NULL NULL, assigned_user_id NUMBER(10) DEFAULT NULL NULL, name VARCHAR2(255) NOT NULL, content CLOB NOT NULL, progress NUMBER(10) NOT NULL, status VARCHAR2(32) NOT NULL, created_at TIMESTAMP(0) NOT NULL, deadline_at TIMESTAMP(0) DEFAULT NULL NULL, finished_at TIMESTAMP(0) DEFAULT NULL NULL, PRIMARY KEY(id))');
        $this->addSql('DECLARE
          constraints_Count NUMBER;
        BEGIN
          SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = \'TASKS\' AND CONSTRAINT_TYPE = \'P\';
          IF constraints_Count = 0 OR constraints_Count = \'\' THEN
            EXECUTE IMMEDIATE \'ALTER TABLE TASKS ADD CONSTRAINT TASKS_AI_PK PRIMARY KEY (ID)\';
          END IF;
        END;');
        $this->addSql('CREATE SEQUENCE TASKS_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TRIGGER TASKS_AI_PK
           BEFORE INSERT
           ON TASKS
           FOR EACH ROW
        DECLARE
           last_Sequence NUMBER;
           last_InsertID NUMBER;
        BEGIN
           SELECT TASKS_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
           IF (:NEW.ID IS NULL OR :NEW.ID = 0) THEN
              SELECT TASKS_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
           ELSE
              SELECT NVL(Last_Number, 0) INTO last_Sequence
                FROM User_Sequences
               WHERE Sequence_Name = \'TASKS_SEQ\';
              SELECT :NEW.ID INTO last_InsertID FROM DUAL;
              WHILE (last_InsertID > last_Sequence) LOOP
                 SELECT TASKS_SEQ.NEXTVAL INTO last_Sequence FROM DUAL;
              END LOOP;
           END IF;
        END;');
        $this->addSql('CREATE INDEX IDX_50586597E104C1D3 ON tasks (created_user_id)');
        $this->addSql('CREATE INDEX IDX_50586597ADF66B1A ON tasks (assigned_user_id)');
        $this->addSql('CREATE TABLE users (id NUMBER(10) NOT NULL, username VARCHAR2(180) NOT NULL, first_name VARCHAR2(180) NOT NULL, last_name VARCHAR2(180) NOT NULL, image_name VARCHAR2(255) DEFAULT NULL NULL, salt VARCHAR2(32) NOT NULL, user_roles CLOB NOT NULL, password VARCHAR2(255) DEFAULT NULL NULL, is_active NUMBER(1) NOT NULL, created_at TIMESTAMP(0) NOT NULL, last_logged_at TIMESTAMP(0) DEFAULT NULL NULL, last_active_at TIMESTAMP(0) DEFAULT NULL NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('COMMENT ON COLUMN users.user_roles IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96E104C1D3 FOREIGN KEY (created_user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597E104C1D3 FOREIGN KEY (created_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597ADF66B1A FOREIGN KEY (assigned_user_id) REFERENCES users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'oracle', 'Migration can only be executed safely on \'oracle\'.');

        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E96E104C1D3');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_50586597E104C1D3');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_50586597ADF66B1A');
        $this->addSql('DROP SEQUENCE users_id_seq');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE users');
    }
}
