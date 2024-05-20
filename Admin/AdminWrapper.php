<?php

namespace letter\Admin;

/**
 * Класс вывода административной части плаигна
 */
class AdminWrapper
{
    private string $currentScreen;
    private mixed $objects;
    private string $out;

    public function __construct($current_screen, $objects = null)
    {
        $this->objects = $objects;
        $this->currentScreen = $current_screen;
    }

    /**
     * Генерирует одну строчку html для таблицы с созданными на сайте формами
     * @param Form $form_obj индекс индекс в массиве форм
     * @param bool $with_tr флажёк указывает должна ли функция добавлять тег <tr></tr> (true/false,default = false)
     * @return string строку таблицы html
     */
    public static function formRowHtml(Form $form_obj, bool $with_tr = false): string
    {
        $td_style = 'style="padding-left: 4px; padding-right: 4px;"';
        $form = $form_obj->returnAsArray();
        $class = 'letter\\types\\'.$form['type'];
        if (class_exists($class)) {
            $type = '<a href="' . admin_url('admin.php?page=letter_forms&form_id='.$form['id']).'" title="Настроить">'.$class::SHOW_NAME.'</a>';
            $color = '';
        } else {
            $type = $form['type'] . ' отсутствует';
            $color = ' style="color: green;"';
        }
        $out = '<td>' . $form['id'] . '</td><td ' . $td_style . '>' .
            '<form method="post" id="form_' . $form['id'] . '" name="update_form">
        <input type="hidden" name="id" form="form_' . $form['id'] . '"  value="' . $form['id'] . '">
        <input type="hidden" name="todo" form="form_' . $form['id'] . '" value="2">
        <input type="text" name="name" form="form_' . $form['id'] . '" value="' . $form['name'] . '"></td></form>
        <td' . $color . ' ' . $td_style . '>' . $type . '</td><td id="prog_bar_' . $form['id'] . '">
        </td><form method="post" id="delete_form_' . $form['id'] . '" name="delete_form">
        <input type="hidden" name="id" form="delete_form_' . $form['id'] . '" value="' . $form['id'] . '"></td>
        <td><input type="hidden" name="todo" form="delete_form_' . $form['id'] . '" value="3">
        <input type="submit" form="delete_form_' . $form['id'] . '" name="delete" value="Удалить">
        </form></td>';
        if ($with_tr) {
            $out = '<tr id="tr_form_' . $form['id'] . '">' . $out . '</tr>';
        }
        return $out;
    }

    /**
     * Генерирует таблицу html с формами, созданными на сайте.
     * @return string таблица html.
     */
    public static function formsTableHtml($forms): string
    {
        $out = '<table id="letter_adm_tbl"><tr><td>id</td><td>Название</td><td>Тип</td>
                <td><div style="width: 30px;"></div></td><td>Удалить</td></tr>';
        if ($forms->getFormsCount() > 0){
            foreach (array_keys($forms->forms) as $key) {
                $out = $out . AdminWrapper::formRowHtml($forms->forms[$key], true);
            }
        }
        $out = $out . '</table>';
        return $out;
    }

    /**
     * Генерирует <form> html для добавления новых объектов типа форма в БД.
     * @return string - таблица html
     */
    final public static function addFormHtml(): string
    {
        $select = '<select name="new_type" title="Тип формы">';
        if (!isset(Admin::$listOfTypes)) {
            return false;
        }
        foreach (Admin::$listOfTypes as $type) {
            $type_class = 'letter\\types\\' . $type;
            $select = $select . '<option value=' . $type . '>' . $type_class::SHOW_NAME . '</option>';
        }
        $select = $select . '</select>';
        $out = '<form method="post" id="add_form" name="add_form"><table><tr><td>
            <input type="text" name="new_name" id="new_name" placeholder="Название" title="Название формы"></td>
            <td> ' . $select;
        $out = $out . '</td><td style="width: 30px;" id="prog_bar_add"></td>
            <td><input type="hidden" name="todo" value="1">
            <input type="submit" value="Добавить" name="add"></td></tr></table></form>';
        return $out;
    }

    public function construction() : void
    {
        switch ($this->currentScreen) {
            case 'letter_forms':
                $this->formsPageConstructor();
                break;

            case 'abc':

                break;

            default:
                break;

        }
    }

    private function formsPageConstructor() : void
    {
        $div_style = 'style="width:auto;  float:left; border: 1px solid gray;  margin:6px; padding: 5px;"';
        $this->out = '<div id="Forms_page" ><div id="forms_div" ' . $div_style . '><b>Список форм:</b><br><br>' .
            AdminWrapper::formsTableHtml($this->objects['form_list']) .
            '</div><div id="add_form_div" ' . $div_style . '><b>Добавить:</b><br><br>' .
            AdminWrapper::addFormHtml() .
            '</div></div>';
    }

    public function htmlPrint() : void
    {
        print $this->out;
    }

    public function clearOut() : void
    {
        $this->out = '';
    }

}

