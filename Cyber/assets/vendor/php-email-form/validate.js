(function () {
  "use strict";

  // Seleciona todos os formulários com a classe 'php-email-form'
  let forms = document.querySelectorAll('.php-email-form');

  // Itera sobre cada formulário encontrado
  forms.forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault(); // Evita o envio padrão do formulário

      let thisForm = this;
      let formData = new FormData(thisForm);
      let action = thisForm.getAttribute('action');
      let recaptchaSiteKey = thisForm.getAttribute('data-recaptcha-site-key');

      // Exibe mensagem de carregamento
      let loadingElement = thisForm.querySelector('.loading');
      loadingElement.classList.add('d-block');

      // Remove mensagens de erro e de sucesso ao iniciar o envio
      let errorMessageElement = thisForm.querySelector('.error-message');
      errorMessageElement.classList.remove('d-block');
      let sentMessageElement = thisForm.querySelector('.sent-message');
      sentMessageElement.classList.remove('d-block');

      // Função para enviar o formulário via AJAX
      submitForm(formData, action, recaptchaSiteKey, thisForm);
    });
  });

  // Função para enviar o formulário via AJAX
  function submitForm(formData, action, recaptchaSiteKey, formElement) {
    let headers = { 'X-Requested-With': 'XMLHttpRequest' };

    // Adiciona o token reCaptcha ao formData se estiver ativado
    if (recaptchaSiteKey && typeof grecaptcha !== "undefined") {
      grecaptcha.ready(function () {
        grecaptcha.execute(recaptchaSiteKey, { action: 'php_email_form_submit' })
          .then(token => {
            formData.set('recaptcha-response', token);
            sendFormData(formData, action, headers, formElement);
          })
          .catch(error => {
            displayError(formElement, error);
          });
      });
    } else {
      sendFormData(formData, action, headers, formElement);
    }
  }

  // Função para enviar os dados do formulário via fetch
  function sendFormData(formData, action, headers, formElement) {
    fetch(action, {
      method: 'POST',
      body: formData,
      headers: headers
    })
      .then(response => {
        if (response.ok) {
          return response.text();
        } else {
          throw new Error(`${response.status} ${response.statusText} ${response.url}`);
        }
      })
      .then(data => {
        // Exibe a mensagem de sucesso se o envio foi bem-sucedido
        formElement.querySelector('.loading').classList.remove('d-block');
        formElement.querySelector('.sent-message').classList.add('d-block');
        formElement.reset(); // Limpa o formulário
      })
      .catch(error => {
        // Exibe a mensagem de erro se ocorreu um problema no envio
        displayError(formElement, error);
      });
  }

  // Função para exibir mensagens de erro
  function displayError(formElement, error) {
    formElement.querySelector('.loading').classList.remove('d-block');
    formElement.querySelector('.error-message').innerHTML = error;
    formElement.querySelector('.error-message').classList.add('d-block');
  }

})();
