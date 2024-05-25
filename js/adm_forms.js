/**
 * adm_forms.js
 *
 *
 */
document.addEventListener("DOMContentLoaded", function () {
    this.page_controll = new Forms_page();
});

/**
 * Класс содержит элементы для управления передачей данных ajax
 *
 *
 */
class Forms_page {

    constructor() {
        this.todo = 0;
        this.tempFieldValue = "";
        self = this;
        this.load_gif = new Image(25, 25);
        this.load_gif.src = js_vars.load_img_src;
        this.action = js_vars.ajax_url;
        this.addform = document.getElementById("add_form");
        if (this.addform !== null) {
            this.addform.addEventListener("submit", function (event) {
                event.preventDefault();
                self.send_form(self.addform);
            });
        }

        this.nameFields = document.querySelectorAll('input[name = "name"]');
        this.nameFields.forEach(function (field) {
            field.addEventListener("focus", function (event) {
                self.tempFieldValue = field.value;
            })
            field.addEventListener("blur", function (event) {
                if (self.tempFieldValue !== field.value) {
                    let form = field.form;
                    self.send_form(form);
                    self.tempFieldValue = field.value;
                }
            });
            field.addEventListener("keydown", function (ev) {

                if (ev.key == "Enter") {
                    ev.preventDefault();
                    if (self.tempFieldValue !== field.value) {
                        let form = field.form;
                        self.send_form(form);
                        self.tempFieldValue = field.value;
                    }
                }
            });
        });

        this.forms = document.querySelectorAll('form[name = "update_form"]');
        this.forms.forEach(function (form) {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
            });
        });
        this.forms = document.querySelectorAll('form[name = "delete_form"]');
        this.form = [];
        for (let i = 0; i < this.forms.length; i++) {
            const form = this.forms[i];
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                if (confirm("Подтвердите удаление \r\nУдалить?")) {
                    self.send_form(form);
                }
            });
        }
    }

    send_form(form) {
        var formData = new FormData(form);
        this.todo = formData.get("todo");
        if (formData.get("todo") === 1) {
            var pbId = "prog_bar_add";
        } else if (formData.get("todo") === 2 || formData.get("todo") === 3) {
            var pbId = "prog_bar_" + formData.get("id");
        }

        var progr_bar = document.getElementById(pbId);
        progr_bar.appendChild(this.load_gif);
        //console.log(pbid);
        formData.append('plugin_id', 'pl_lttr_1');
        formData.append('handler', 'Forms');
        var xhr = new XMLHttpRequest();
        xhr.open("POST", this.action, true);
        xhr.setRequestHeader("Accept", "application/json");
        xhr.send(formData);
        xhr.onreadystatechange = function () {
            progr_bar.innerHTML = "";
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    self.jsonHandler(xhr.responseText, formData);
                } else {
                    console.log('An error occurred while submitting the form. Status:' +
                        +xhr.status);
                }
            }
        };
    }

    jsonHandler(responseText, formData) {
        const json = JSON.parse(responseText);
        if (!json.error) {
            console.log("const error not set");
            return;
        }


        if (this.todo === 1) {
            if (json.error !== "0") {
                console.log(json.error);
                return;
            }

            let table = document.getElementById("letter_adm_tbl");
            table.insertAdjacentHTML('beforeend', json.form_html_tr);
            let form = document.getElementById("form_" + json.form_id);
            let deleteForm = document.getElementById("delete_form_" + json.form_id);
            let nameField = form.querySelector('[name = "name"]');

            deleteForm.addEventListener("submit", function (event) {
                event.preventDefault();
                if (confirm("Подтвердите удаление \r\nУдалить?")) {
                    self.send_form(deleteForm);
                }
            });

            nameField.addEventListener("blur", function (event) {
                let form = nameField.form;
                self.send_form(form);
            });
        }

        if (this.todo === 2) {
            if (this.error !== "0") {
                let updForm = document.getElementById("form_" + formData.get("id"));
                let updField = updForm.querySelector('[name = "name"]');
                updField.value = this.tempFieldValue;
                console.log(json.error);
                return;
            }
        }

        if (this.todo === 3) {
            if (json.error !== "0") {
                console.log(json.error);
                return;
            }
            let formTableString = document.getElementById("tr_form_" + json.form_id);
            formTableString.remove();
        }
    }
}