<?php

namespace letter\Admin;


class Form
{
    private $id;
    private $name;
    private $type;
    private string $jSettings;

    public function __construct($id, $type, $name = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Функция установки значения this::jsettings
     * @param $settings - строка json
     */
    public function setJSettings($settings): void
    {
        $this->jSettings = $settings;
    }

    public function getJSettings(): string{
        if (isset($this->jSettings) && !empty($this->jSettings)){
            return $this->jSettings;
        }
        else{
            return '';
        }
    }

    public function returnAsArray()
    {
        $array['id'] = $this->id;
        $array['name'] = $this->name;
        $array['type'] = $this->type;
        return $array;
    }

    public function setAsArray($array)
    {
        if (!empty($array['name'])) {
            $this->name = $array['name'];
        }
        if (!empty($array['type'])) {
            $this->type = $array['type'];
        }
    }
}
