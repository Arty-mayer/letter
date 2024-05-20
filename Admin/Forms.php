<?php

namespace letter\Admin;


/**
 * Класс инкапсулирует массив объектов типа Form
 */
class Forms
{
    private \wpdb $db;
    private string $dbTable;
    public array $forms;
    private int $formsCount;

    /**
     *Конструктор класса. "Подключает" объек wpdb для работы с БД
     */
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->dbTable = $this->db->prefix . 'letters_forms';
    }

    /**
     * @return int
     */
    public function getFormsCount(): int
    {
        return $this->formsCount;
    }

    /**
     * Загружает "формы" из БД и создает массив с ними
     * @return void
     */
    public function formLoadFromDb(int $id = 0) : bool
    {

        if ($id == 0){
            return false;
        }
        $query = 'SELECT * FROM ' . $this->dbTable . ' WHERE id = ' . $id . ';';
        $result = $this->db->get_row($query, 'ARRAY_A');
        if (!is_array($result)){
            return false;
        }
        $this->forms[$id] = new Form ($result["id"], $result["type"], $result["name"]);
        if (isset($result['jSettings'])){
            $this->forms[$id] -> setJSettings($result['jSettings']);
        }
        else {
            $this->forms[$id] -> setJSettings('');
        }
        return true;
    }

    public function formsLoadFromDb(): void
    {
        $query = 'SELECT id, name, type FROM ' . $this->dbTable . ';';
        $result = $this->db->get_results($query, "ARRAY_A");
        $i = 0;
        foreach ($result as $value) {
            $this->forms[$value["id"]] = new Form ($value["id"], $value["type"], $value["name"]);
            $i++;
        }
        $this->formsCount = $i;
    }

    /**
     * добавляет новую форму в БД ввиде новой строки
     * @param $date - данные для добавления (массив)
     * @return int - ID в БД добавленной формы | 0 если не удалось добавить
     */
    private function addFormInDb($date): int
    {

        if (empty($date['type'])) {
            return 0;
        } else {
            $date_type[0] = "%s";
        }
        if (!empty($date['name'])) {
            $date_type[1] = "%s";
        }

        $this->db->insert($this->dbTable, $date, $date_type);
        if ($this->db->insert_id == 0) {
            return 0;
        }
        $query = 'Select * from ' . $this->dbTable . ' where id = ' . $this->db->insert_id . ';';
        $result = $this->db->get_row($query, "ARRAY_A");
        if (is_array($result)) {
            $this->forms[$result['id']] = new Form($result['id'], $result['type'], $result['name']);
            $return = $result['id'];
        } else {
            $return = 0;
        }
        return $return;
    }

    /**
     * вносит изменения в БД
     * @param $id - ID формы в БД
     * @param $date - данные для изменения (массив)
     * @return bool - true -успешно | false - безуспешно
     */
    private function updateFormInDb($id, $date): bool
    {
        $returnKey = true;
        if (empty($id) || $id == 0) {
            return false;
        }
        if (!isset($date['name'])) {
            return false;
        }
        $data_type[0] = "%s";
        $where['id'] = $id;
        $where_type[0] = '%d';
        $result = $this->db->update($this->dbTable, $date, $where, $data_type, $where_type);

        if ($result === false) {
            $returnKey = false;
        }
        return $returnKey;
    }

    /**
     * Удаляет форму (строку) из БД
     * @param $id - ID формы в БД
     * @return bool - результат true | false
     */
    private function deleteFormFromDb($id): bool
    {
        if ($id == 0) {
            return false;
        }
        $result = $this->db->delete($this->dbTable, ['id' => $id], ['%d']);
        if (!$result || $result == 0) {
            return false;
        }
        return true;
    }

    /**
     * обработчик ajax-запросов добавления, удаления, редактирования(переименования) форм  в БД
     * @return array - ответ для Фронта .
     */
    public function ajaxHandler(): array
    {
        if (empty($_POST['todo'])) {
            $answer ['error'] = "the action is not set";
            return $answer;
        }
        $answer['error'] = "0";
        $todo = (int)$_POST['todo'];

        if ($todo == 1) {
            $answer['todo'] = 1;
            $result = $this->addFormInDb(["type" => $_POST['new_type'], 'name' => $_POST['new_name']]);
            if ($result > 0) {
                $form_html = AdminWrapper::formRowHtml($this->forms[$result], true);
                $answer ['error'] = "0";
                $answer ['form_html_tr'] = $form_html;
                $answer ['form_id'] = $result;
            } else {
                $answer ['error'] = "Problem in Database";
            }
        } elseif ($todo == 2) {
            $answer['todo'] = 2;
            if ($this->updateFormInDb((int)$_POST['id'], ['name' => $_POST['name']])) {
                $answer ['error'] = "0";
                $answer ['form_id'] = (int)$_POST['id'];
            } else {
                $answer ['error'] = 'failed to make changes to the database';
            }
        } elseif ($todo == 3) {
            $answer['todo'] = 3;
            if ($this->deleteFormFromDb((int)$_POST['id'])) {
                $answer ['error'] = "0";
                $answer ['form_id'] = (int)$_POST['id'];
            } else {
                $answer['error'] = 'failed to delete string from database';
            }
        }
        return $answer;
    }
}


