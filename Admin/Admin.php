<?php

namespace letter\Admin;

use JetBrains\PhpStorm\NoReturn;
use letter\Admin\AdminWrapper;
use letter\Admin\Forms;
use letter\Admin\Form;
use letter\includes\interfaceType;
use letter\types\Brief;


/**
 * Класс Admin - набор методов для административной части плагина
 */
class Admin {
	/**
	 * содержит массив имен классов типов форм
	 * @var array
	 */
	public static array $listOfTypes;
	public Form $form;
	public static array $view;
	public mixed $Type;

	/**
	 *конструктор класса
	 */
	public function __construct() {

		$this->getCurrentView();
		$this->addActions();
	}

	/**
	 * Регистрирует хуки
	 * @return void
	 */
	private function addActions(): void {

		add_action( 'wp_ajax_letter_admin', [ $this, 'letter_admin_ajax' ] );
		add_action( 'admin_menu', [ $this, 'letter_admin_menu' ] );
		if (Admin::$view['form_id'] == 0) {
			add_action( 'admin_enqueue_scripts', [ $this, 'letter_script_adm' ] );
		}
	}

	/**
	 * определяет текущий view (screen).
	 * (на основании данных этой функции определяется какой контент генерировать, какие скрипты подключать и т.п.)
	 * @return void
	 */
	private function getCurrentView() {
		Admin::$view['current_screen'] = ( isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ) ? $_GET['page'] : '';
		$result                        = false;
		$form_id                       = 0;

		if ( Admin::$view['current_screen'] === 'letter_forms' ) {
			$form_id = ( isset( $_GET['form_id'] ) && ! empty( $_GET['form_id'] ) ) ? (int) $_GET['form_id'] : 0;

			if ( $form_id != 0 ) {
				$result = $this->setFormInstance( $form_id );
			}
		}

		if ( $result ) {
			Admin::$view['form_id'] = $form_id;
		} else {
			Admin::$view['form_id'] = 0;
		}
	}

	/**
	 * создает экземпляр Forms и через него создает для дальнейшего использования экземпляр Form c определенным.
	 * в параметре form_id. Заодно проверяется наличие формы с данным id в БД.
	 *
	 * @param int $form_id id нужной формы.
	 *
	 * @return bool true | false в случае успешного и не успешного выполнения соответственно.
	 */
	private function setFormInstance( int $form_id ): bool {
		$forms = new Forms();
		if ( $forms->formLoadFromDb( $form_id ) ) {
			$this->form = $forms->forms[ $form_id ];
			$className = 'letter\\types\\' . $this->form->returnAsArray()['type'];
			if (class_exists($className)){
				$this->Type = new $className;
				return true;
			}
		}
		return false;
	}

	/**
	 * создает меню плагина для административной панели
	 * @return void
	 */
	public function letter_admin_menu() {
		$plugin_prefix = 'letter_';
		$sub_rules     = 'edit_pages';
		$icon_base64   = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iMjQwMC4wMDAwMDBwdCIgaGVpZ2h0PSIxNDYzLjAwMDAwMHB0IiB2aWV3Qm94PSIwIDAgMjQwMC4wMDAwMDAgMTQ2My4wMDAwMDAiCiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBtZWV0Ij4KPG1ldGFkYXRhPgpDcmVhdGVkIGJ5IHBvdHJhY2UgMS4xNiwgd3JpdHRlbiBieSBQZXRlciBTZWxpbmdlciAyMDAxLTIwMTkKPC9tZXRhZGF0YT4KPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsMTQ2My4wMDAwMDApIHNjYWxlKDAuMTAwMDAwLC0wLjEwMDAwMCkiCmZpbGw9IiMwMDAwMDAiIHN0cm9rZT0ibm9uZSI+CjxwYXRoIGQ9Ik0xMzEwMCAxNDYyMyBjLTI4NTMgLTIzIC02MTk5IC04OSAtODI2MCAtMTYzIC01MDYgLTE4IC01NzMgLTIwCi0xMDcwIC00MCAtMTI0MCAtNTAgLTI2NDAgLTEyOSAtMjkxNiAtMTY1IGwtNDAgLTYgLTE2IDExMyBjLTIxIDE0NSAtMzcgMTkzCi02MyAxOTMgLTI3IDAgLTQyIC00OCAtNjMgLTE5OCAtMTQgLTEwNCAtMTkgLTExOCAtMzcgLTEyMiAtMTEgLTIgLTEwMSAtMTMKLTIwMCAtMjQgLTIwMSAtMjQgLTM2MyAtNTQgLTQwNyAtNzcgLTM2IC0xOSAtMzQgLTQ0IDYgLTYzIDQyIC0yMCAyMTQgLTQ3CjQyNCAtNjggbDE4MiAtMTcgLTUgLTMxIGMtMTIgLTc1IC00MCAtODAyIC01NSAtMTQwMCAtMzUgLTE0NjIgLTQ4IC00MjU5IC0zMQotNjkwNSAxMSAtMTc4OSAxOCAtMjQ2NSA0MiAtNDU5MiBsMTMgLTEwNTggMTc1IDAgYzk3IDAgNDE5IDQgNzE2IDEwIDI5NyA1Cjc5NyAxNCAxMTEwIDIwIDIzMzMgNDIgMjg2MiA1MiA0MjE1IDgwIDE1MTMgMzEgMzI1NSA3MCA0MDQwIDkwIDIyMyA2IDY3NSAxNwoxMDA1IDI1IDQ2NzUgMTE3IDg0ODEgMjU2IDEwNDcwIDM4MSAzMDkgMTkgODc1IDYyIDk4NSA3NCA0NyA2IDg4IDEwIDkxIDEwIDQKMCA5IC0xNSAxMiAtMzMgOSAtNTcgNDggLTE0NyA2OCAtMTU3IDMyIC0xOCA2MyAyNCA4OSAxMjMgbDIzIDg2IDQ2IDYgYzIzMgozMSAzNDUgNjAgMzQ5IDkxIDIgMTYgLTUgMjMgLTM1IDMyIC00OSAxNCAtMTQ3IDI5IC0yNTEgMzggLTgzIDcgLT' . 'gzIDcgLTc4CjMzIDMgMTQgMTIgODcgMjEgMTYxIDc0IDY1NSAxMjYgMTY2OSAxNjYgMzI3MCAyNyAxMTAzIDIxIDQyNDEgLTExIDUyMTAgLTUKMTYyIC0xNCA0NjIgLTIwIDY2NSAtMzIgMTEyNiAtODEgMjExMCAtMTUxIDMwMDUgLTM2IDQ3OCAtMTEwIDk0NSAtMTY0IDEwNTEKLTE5IDM3IC01OCAzOSAtODAgNSAtMjkgLTQ0IC02NSAtMjE3IC05NSAtNDU5IC0yODAgLTIyNjUgLTI2MiAtOTY0MyAzMAotMTI1MTcgMTYgLTE1NyAzMiAtMzExIDM2IC0zNDMgbDcgLTU4IC0yNzQgOCBjLTQwNiAxMiAtMjk1MyA4IC0zNzk5IC02IC05MjIKLTE1IC0yMTU3IC0zOCAtMjc0NSAtNTEgLTIyMzIgLTUwIC0zNDcwIC04MSAtNDQzNSAtMTEwIC0xODQgLTYgLTU2NyAtMTcKLTg1MCAtMjUgLTE2NDQgLTQ4IC01MzAyIC0xNzAgLTc1MTUgLTI1MCAtMTY4NSAtNjEgLTIzMzQgLTg1IC0yNTY1IC05NCAtMTI0Ci01IC0yMjYgLTggLTIyNyAtNyAtMSAxIDMgNzc1IDkgMTcyMSAxMSAxODcwIDUgNjEzOCAtMTIgNzQ2NSAtMjQgMjAzMCAtNjQKMzU2NiAtMTA2IDQxNzYgbC03IDkxIDg2IC04MiBjMzY1IC0zNTAgMTg4NiAtMTQzMSA0MzE2IC0zMDY0IDYwMiAtNDA1IDYyMQotNDE3IDk4NSAtNjE0IDEwODggLTU4NyAyMzA2IC0xMjY0IDMyMjEgLTE3OTIgNTk1IC0zNDMgNzA5IC00MDcgMTA5MCAtNjEwCjIxNSAtMTE0IDU0OCAtMjkzIDc0MCAtMzk4IDI5NCAtMTU5IDUyNCAtMjg2IDEzMDAgLTcxMyBsNjEgLTM0IDQ2OSAzNTMKYzIwNDUgMTUzNCA0MTQ0IDMwMDUgNjcwNyA0Njk5IDEzMzYgODgzIDI2NzIgMTc3OCAyOTYwIDE5ODQgMjY4IDE5MiA0NjMgMzYzCjQzOSAzODcgLTY1IDY1IC0xMDkxIC00MzMgLTIzNTYgLTExNDYgLTI0MjUgLTEzNjUgLTUzOTggLTMzOTggLTgwNDEgLTU0OTgKbC0yMTEgLTE2OCAtNTE0IDM0MiBjLTEwMjYgNjgzIC0xNTUxIDEwMTUgLTIwMTkgMTI3OSAtMTE3OCA2NjIgLTIzMjcgMTI3MgotMzUxNSAxODY1IC0zMzYgMTY4IC00NTEgMjMyIC03MTUgMzk1IC0xNzc5IDExMDAgLTM0MTQgMjA3MCAtNDI3NSAyNTM4IC00MTgKMjI4IC02MDIgMzE0IC02OTAgMzI3IGwtNDUgNiAwIDQ4IGMwIDQ0IDIgNDkgMjMgNDkgMTIgMCAxMzcgLTcgMjc3IC0xNSAyMDc1Ci0xMTggNzE2NyAtMTQ1IDEyMjY1IC02NSA1NDY3IDg2IDk3NzYgMjgwIDEwMTE1IDQ1NSA2OSAzNiAyMiA2NSAtMTUzIDk0Ci01MjAgODkgLTIxMDcgMTUwIC00NzE3IDE4MSAtNjcyIDggLTQ2NTUgMTAgLTU1NTAgM3oiLz4KPC9nPgo8L3N2Zz4K';
		$page_title    = 'Letter ' . LETTERS_VERSION;
		$menu_title    = 'Letter';
		$position      = '10';
		$capability    = $sub_rules;
		$menu_slug     = $plugin_prefix . 'overview';
		$function      = [ $this, 'letter_overview' ];

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_base64, $position );

		$sub_m    = array();
		$sub_m[0] = array(
			'main_page'         => $plugin_prefix . 'overview',
			'page_title'        => 'Letter - overview', //page title
			'menu_title'        => 'Обзор', //menu title
			'capability'        => $sub_rules, //capability
			'menu_slug'         => $plugin_prefix . 'overview', //menu slug
			'callback_function' => [ $this, $plugin_prefix . 'overview' ], //callback function
		);
		$sub_m[1] = array(
			'main_page'         => $plugin_prefix . 'overview',
			'page_title'        => 'Формы - Letter', //page title
			'menu_title'        => 'Формы', //menu title
			'capability'        => $sub_rules, //capability
			'menu_slug'         => $plugin_prefix . 'forms', //menu slug
			'callback_function' => [ $this, $plugin_prefix . 'forms' ], //callback function
		);

		$sub_m[2] = array(
			'main_page'         => $plugin_prefix . 'overview',
			'page_title'        => 'Настройки', //page title
			'menu_title'        => 'Настройки', //menu title
			'capability'        => $sub_rules, //capability
			'menu_slug'         => $plugin_prefix . 'settings', //menu slug
			'callback_function' => [ $this, $plugin_prefix . 'settings' ] //callback function
		);
		//добавляем подменю
		foreach ( $sub_m as $value ) {
			add_submenu_page( $value['main_page'], $value['page_title'], $value['menu_title'], $value['capability'],
				$value['menu_slug'], $value['callback_function'] );
		}
	}

	/**
	 * реализует подключение файла скрипта
	 * @return void
	 */
	public function letter_script_adm(): void {
		$js_vars['ajax_url']     = admin_url( 'admin-ajax.php' ) . '?action=letter_admin';
		$js_vars['load_img_src'] = LETTER_PLUGIN_URL . 'img/loading.gif';
		$current_screen          = get_current_screen();
		if ( $current_screen->id === 'letter_page_letter_forms' ) {
			$file    = 'adm_forms.js';
			$version = 1.0;
			$depend  = array();
		}

		if ( ! empty( $file ) ) {

			wp_enqueue_script( 'letter_adm_forms', LETTER_PLUGIN_URL . 'js/' . $file, $depend, $version, true );
			wp_localize_script( 'letter_adm_forms', 'js_vars', $js_vars );
		}
	}


	/**
	 * функцция Ajax-ответчик.
	 * @return void
	 */
	#[NoReturn] public function letter_admin_ajax(): void {
		Admin::setListOfTypes();
		if ( ! isset( $_POST['plugin_id'] ) || $_POST['plugin_id'] != "pl_lttr_1" ) {
			wp_die();
		}
		if (empty($_POST['handler'])){
			wp_die();
		}
		$class = 'letter\\Admin\\' . $_POST['handler'];
		if ( ! class_exists( $class ) ) {
			$answer ['error'] = "Handler not exist";
		} else {
			$ajax_handler = new $class;
			if ( method_exists( $ajax_handler, 'ajaxHandler' ) ) {
				$answer = $ajax_handler->ajaxHandler();
			} else {
				$answer['error'] = 'handler not supports Ajax (method not exist)';
			}
		}
		$json = json_encode( $answer );
		print $json;
		wp_die();
	}

// функции, реализующие пункты меню! !!!!!!!!!!!!!!!!!

	/**
	 * реализует пункт меню - "Обзор"
	 * @return void
	 */
	public function letter_overview(): void {
		$wrapper = new AdminWrapper();
		$wrapper->overviewPageConstructor();
		$wrapper->htmlPrint();
	}

	/**
	 * реализует пункт меню - "Формы"
	 * @return void
	 */
	public function letter_forms(): void {
		$wrapper = new AdminWrapper();

		if ( Admin::$view['form_id'] > 0 ) {
			$wrapper->setObjects(['Type' => $this->Type]);
			$wrapper->addFormsStyle();
			$wrapper->formPageConstructor();
		} else {
			Admin::setListOfTypes();
			$form_list = new Forms;
			$form_list->formsLoadFromDb();
			$wrapper->setObjects( [ 'form_list' => $form_list ] );
			$wrapper->formsPageConstructor();
		}

		$wrapper->htmlPrint();
	}

	/**
	 * реализует пункт меню - Настройки
	 * @return void
	 */
	public function letter_settings(): void {
		$wrapper = new AdminWrapper();
		$wrapper->settingsPageConstructor();
		$wrapper->htmlPrint();
	}


	/**
	 * метод составляет список типов (классов) возможных документов на основании их присутствия в директрии.
	 * @return  "false" - если нет ни одного файла | массив, содержащий имена классов и их название из константы класса
	 */
	public static function setListOfTypes(): bool {
		$types_dir = LETTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'types';
		if ( is_dir( $types_dir ) ) {
			$registered_types = Admin::dirRead( $types_dir );
			if ( ! $registered_types ) {
				unset( $registered_types );
			}
		}
		if ( isset( $registered_types[0] ) && ! empty( $registered_types[0] ) ) {
			Admin::$listOfTypes = $registered_types;

			return true;
		} else {
			return false;
		}
	}

	/**
	 * вспомогательный метод. Читает папку с типами форм.
	 *
	 * @param $path содержит путь директории с классами типов
	 *
	 * @return array|bool либо массив имен классов либо false
	 */
	private static function dirRead( $path ): bool|array {
		if ( ! ( $dir = opendir( $path ) ) ) {
			return false;
		}
		$i = 0;
		while ( ( $file = readdir( $dir ) ) !== false ) {
			if ( $file == "." || $file == ".." || $file == "index.php" ) {
				continue;
			}
			if ( substr_count( $file, '.php' ) == 1 ) {
				$class_name      = str_replace( '.php', '', $file );
				$full_class_name = 'letter\\types\\' . $class_name;
				if ( class_exists( $full_class_name ) ) {
					$registered_types [ $i ] = $class_name;
					$i ++;
				}
			}
		}

		return ( ! empty ( $registered_types[0] ) ) ? $registered_types : false;
	}
}
