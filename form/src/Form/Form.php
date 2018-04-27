<?php
 
namespace Drupal\form\Form;

use Drupal\Core\Form\FormBase;                   // Базовый класс Form API
use Drupal\Core\Form\FormStateInterface;              // Класс отвечает за обработку данных


/**
 * Наследуемся от базового класса Form API
 * @see \Drupal\Core\Form\FormBase
 */
class Form extends FormBase {

    // метод, который отвечает за саму форму - кнопки, поля
    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['name'] = [
            '#type' => 'textfield',
            '#description' => $this->t('The name can not contain digits'),
            '#required' => TRUE,
        ];
        $form['surname'] = [
            '#type' => 'textfield',
            '#description' => $this->t('Surname can not contain digits'),
            '#required' => TRUE,
        ];
        $form['title'] = [
            '#type' => 'textfield',
            '#description' => $this->t('Message subject.'),
            '#required' => TRUE,
        ];
        $form['text'] = [
            '#type' => 'textfield',
            '#description' => $this->t('Your message.'),
            '#required' => TRUE,
        ];
        $form['email'] = [
            '#type' => 'email',
            '#description' => $this->t('Email.'),
            '#required' => TRUE,
        ];


        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;
    }

    // form name
    public function getFormId() {
        return 'form_form_form';
    }

    // fun validate
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $name = $form_state->getValue('name');
        $surname = $form_state->getValue('surname');
        $email = $form_state->getValue('email');
        $is_number_name = preg_match("/[\d]+/", $name, $match);
        $is_number_surname = preg_match("/[\d]+/", $surname, $match);

        if ($is_number_name > 0) {
            $form_state->setErrorByName('name', $this->t('The name contains a number - %name.', ['%name' => $name]));
        }

        if ($is_number_surname > 0) {
            $form_state->setErrorByName('surname', $this->t('Surname contains a number.', ['%surname' => $surname]));
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $form_state->setErrorByName('email', $this->t('Invalid Email - %email.', ['%email' => $email]));
        }
    }



    // submit
    public function submitForm(array &$form, FormStateInterface $form_state) {

        $name = $form_state->getValue('name');
        $surname = $form_state->getValue('surname');
        $email = $form_state->getValue('email');
        drupal_set_message(t('You sent an application!');

        $arr = array(
            'properties' => array(
                array(
                    'property' => 'email',
                    'value' => $email
                ),
                array(
                    'property' => 'firstname',
                    'value' => $name
                ),
                array(
                    'property' => 'lastname',
                    'value' => $surname
                )
            )
        );
        $post_json = json_encode($arr);
        $hapikey = "43cc1d10-d80a-402c-8f89-6c5963390747";
        $endpoint = 'https://api.hubapi.com/contacts/v1/contact?hapikey=' . $hapikey;
        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        @curl_setopt($ch, CURLOPT_URL, $endpoint);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = @curl_exec($ch);
        $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errors = curl_error($ch);
        @curl_close($ch);
        echo "curl Errors: " . $curl_errors;
        echo "\nStatus code: " . $status_code;
        echo "\nResponse: " . $response;

        }



}

