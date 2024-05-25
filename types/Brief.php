<?php

namespace letter\types;

use letter\includes\interfaceType;

class Brief implements interfaceType {
	const SHOW_NAME = "Письмо";
	const T_VERSION = 1;
	private $out;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'briefScriptAdmin' ] );
	}

	public function briefScriptAdmin() {
		wp_enqueue_script( 'jspdf', LETTER_PLUGIN_URL . 'js/lib/jspdf.umd.js', array(), null, true );
		wp_enqueue_script( 'jspdf-autotable', LETTER_PLUGIN_URL .
		                          'js/lib/jspdf.plugin.autotable.js', array('jspdf'), null, true );
		//wp_enqueue_script('jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', array(), null, true);
		//wp_enqueue_script('jspdf-autotable', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js', array('jspdf'), null, true);

	}

	public function setJs() {
		return 'df';
	}

	public function constructSettingsPage() {

		$this->out = $this->mainControlPanel() . '<button id="generate-pdf">Generate PDF</button>
<script>
    document.getElementById(\'generate-pdf\').addEventListener(\'click\', function () {
        // Создаем новый экземпляр jsPDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        

        // Пример данных для таблицы
        const tableData = [
            { id: 1, name: \'John Doe\', email: \'john@example.com\', country: \'USA\' },
            { id: 2, name: \'Jane Doe\', email: \'jane@example.com\', country: \'Canada\' },
            { id: 3, name: \'Max Mustermann\', email: \'max@example.com\', country: \'Germany\' }
        ];

        // Создаем таблицу с помощью autotable
        doc.autoTable({
            head: [[\'ID\', \'Name\', \'Email\', \'Country\']], // Заголовок таблицы
            body: tableData.map(data => [data.id, data.name, data.email, data.country]), // Данные для таблицы
        });

        // Сохраняем PDF-документ
        
        doc.save(\'document.pdf\');
    });

</script>';

		return $this->out;
	}

	private function mainControlPanel() {

		$out = '<div id="mainCP" class="letter-set-container" ><form method="post" id=""><select name="block_type" id="new_block_type"><option value="1">1</option>' .
		       '<option value="2">2</option></select></form></div>';

		return $out;
	}

	public function getHtml(): string {
		if ( isset( $this->out ) && ! empty( $this->out ) ) {
			return $this->out;
		} else {
			return '';
		}
	}

	public function ajaxHandler() {

		return 1;
	}
}
