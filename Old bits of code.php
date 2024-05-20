<?php

/**
 * Генерирует одну строчку html для таблицы с созданными на сайте формами
 * @param int $f_index индекс индекс в массиве форм
 * @param bool $with_tr флажёк указывает должна ли функция добавлять тег <tr></tr> (true/false,default = false)
 * @return string строку таблицы html
 *//*
    public function formRowHtml($f_index, $with_tr = false)
    {
        $form = $this->forms[$f_index]->returnAsArray();
        if (class_exists($form['type'])) {
            $type = $form['type']::SHOW_NAME;
            $color = '';
        } else {
            $type = $form['type'] . ' отсутствует';
            $color = ' style="color: green;"';
        }
        $out = '<td>' . $form['id'] . '</td><td><form method="post" id="form_' . $form['id'] . '" name="update_form">
        <input type="hidden" name="id" form="form_' . $form['id'] . '" value="' . $form['id'] . '">
        <input type="hidden" name="todo" form="form_' . $form['id'] . '" value="2">
        <input type="text" name="name" form="form_' . $form['id'] . '" value="' . $form['name'] . '"></td></form>
        <td' . $color . '>' . $type . '</td><form method="post" id="delete_form_' . $form['id'] . '" name="delete_form">
        <input type="hidden" name="id" form="delete_form_' . $form['id'] . '" value="' . $form['id'] . '"></td>
        <td><input type="hidden" name="todo" form="delete_form_' . $form['id'] . '" value="3">
        <input type="submit" form="delete_form_' . $form['id'] . '" name="delete" value="Удалить">
        </form></td><td id="prog_bar_' . $form['id'] . '"></td>';
        if ($with_tr) {
            $out = '<tr id="tr_form_' . $form['id'] . '">' . $out . '</tr>';
        }
        return $out;
    }

    /**
     * Генерирует таблицу html с формами, созданными на сайте.
     * @return string таблица html.
     *//*
    public function formsTableHtml()
    {

        $out = '<table id="letter_adm_tbl"><tr><td>id</td><td>Название</td><td>Тип</td><td>Удалить</td></tr>';
        foreach (array_keys($this->forms) as $key) {
            $out = $out . $this->formRowHtml($key, true);
        }
        $out = $out . '</table>';
        return $out;
    }

    /**
     * Генерирует <form> html для добавления новых объектов типа форма в БД.
     * @return string - таблица html
     *//*
public function addFormHtml()
{

    $select = '<select name="new_type" form="add_form">';
    if (!is_array(Admin::$listOfTypes)){
        return false;
    }
    foreach (Admin::$listOfTypes as $type) {
        $select = $select . '<option value=' . $type . '>' . $type::SHOW_NAME . '</option>';
    }
    $select = $select . '</select>';
    $out = '<table><tr><td>&nbsp;</td><td>Название</td><td>Тип</td>
<td></td></tr><tr><td></td><td><form method="post" id="add_form"><input type="hidden" name="todo" value="1">
<input type="text" name="new_name" form="add_form"></td><td>' . $select . '</td>
<td><input type="submit" value="Добавить" form="add_form"></form></td><td id="prog_bar_add"></td></tr></table>';
    return $out;
}*/
