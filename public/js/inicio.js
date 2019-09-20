window.onload = inicio();

    function inicio(){

      if(localStorage.getItem('nombre')){
        window.location.href = "https://www.desposte.tk/solicitud";
      }

      if(localStorage.getItem('usuario') || localStorage.getItem('password')){
        document.getElementById('usuario').value = localStorage.getItem('usuario');
        document.getElementById('password').value = localStorage.getItem('password');
      }
    }

    document.getElementById('inicioSesionUsuario').addEventListener('click', function(e){
        e.preventDefault();
        
        var usuario = document.getElementById('usuario');
        var password = document.getElementById('password');

        if(usuario.value.length > 0 && password.value.length > 0){
          
          if(document.getElementById('checkBoxRemember').checked == true){
            localStorage.setItem("recordar",1);
          }

          if(sessionStorage.getItem("CambioIniciado")){
            sessionStorage.removeItem("CambioIniciado");
          }

            var objeto = {
              usuario:usuario.value,
              password:password.value
            };

            QueryIniciarSistema(objeto);

        }else{
            e.preventDefault();
            document.getElementById('botoErrorInicioSesion').click();
        }

    })

    document.getElementById('forgotPassword').addEventListener('click', function(){
      
      document.getElementById('modalHeaderInicio').innerHTML = `<b>Renovar Contraseña</b>`;

      document.getElementById('bodyModalContentInicio').innerHTML = `
        <p><b>Ingresa el correo al que desees recibir la nueva contraseña:
          <input type="text" id="emailUser" class="form-control" placeholder="Ingresa tu Correo"></b>
        </p>
      `;

      document.getElementById('footerModalInicio').innerHTML = `
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="sendEmailRecovery" onclick="SendEmailRecovery(1)">Enviar</button>
      `;
      
      document.getElementById('botoErrorInicioSesion').click();
    });

    function SendEmailRecovery(parametro){

      if(parametro == 1){
        var correo = document.getElementById('emailUser').value;

        if(correo.length == 0){
          var parrafoErrorForgotPass = document.querySelector('p#messageForgotContrasena');

          if(parrafoErrorForgotPass){
            document.getElementById('bodyModalContentInicio').removeChild(parrafoErrorForgotPass);
          }

          document.getElementById('bodyModalContentInicio').innerHTML += `
          <p style="font-weight:bold; font-size: 1rem; color:red" id="messageForgotContrasena">- Debes de diligenciar el campo.</p>
          `;
        }else if(correo.length < 10 && correo.length > 0){
          var parrafoErrorForgotPass = document.querySelector('p#messageForgotContrasena');

          if(parrafoErrorForgotPass){
            document.getElementById('bodyModalContentInicio').removeChild(parrafoErrorForgotPass);
          }
          document.getElementById('bodyModalContentInicio').innerHTML += `
          <p style="font-weight:bold; font-size: 1rem; color:red" id="messageForgotContrasena">- Debes ingresar un correo valido.</p>
          `;
        }else{
          var objeto = {
            correo: correo,
          }
          QueryRestablecerUsuario(objeto);
        }
      }else{
        var parrafoErrorForgotPass = document.querySelector('p#messageForgotContrasena');
        if(parrafoErrorForgotPass){
          document.getElementById('bodyModalContentInicio').removeChild(parrafoErrorForgotPass);
        }
        document.getElementById('bodyModalContentInicio').innerHTML += `
          <p style="font-weight:bold; font-size: 1rem; color:red" id="messageForgotContrasena">
            El correo proporcionado no coincide con ninguno de los asociados a Cercafe
          </p>
          `;
      }
    }

    function CorreoEnviadoModal(){
      document.getElementById('modalHeaderInicio').innerHTML = `<b>Renovar Contraseña</b>`;

      document.getElementById('bodyModalContentInicio').innerHTML = `
        <p><b>Correo enviado con Exito</b>
          <br>
        Pronto será reestablecido tu usuario en desposte.</p>
      `;

      document.getElementById('footerModalInicio').innerHTML = `
        <button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
      `;
    }

    function RestablecerModalInicio(){

      document.getElementById('modalHeaderInicio').innerHTML = `
        <b>Error de inicio</b>
      `;
      document.getElementById('bodyModalContentInicio').innerHTML = `
        <p id="contentErrorModalInicio">Debes ingresar un usuario y contraseña para ingresar.</p>
      `;
      document.getElementById('footerModalInicio').innerHTML = `
        <button type="button" class="btn btn-danger" data-dismiss="modal">Ok</button>
      `;
    }

    function QueryRestablecerUsuario(objeto){
      
      var ajax = new XMLHttpRequest();
      ajax.open("GET","https://www.desposte.tk/ReestablecerUsuario?data="+ encodeURIComponent(JSON.stringify(objeto)),true);
      ajax.setRequestHeader("Content-Type", "application/json");
      ajax.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            var respuesta = ajax.responseText;
            if(respuesta == "OK"){
              localStorage.removeItem("usuario");
              localStorage.removeItem("password");
              CorreoEnviadoModal();
            }else{
              SendEmailRecovery(2);
            }
          }
      }
      ajax.send();
    }

    function QueryIniciarSistema(objeto){
      var ajax = new XMLHttpRequest();
      ajax.open("GET","https://www.desposte.tk/inicioSesion?data="+ encodeURIComponent(JSON.stringify(objeto)),true);
      ajax.setRequestHeader("Content-Type", "application/json");
      ajax.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            var response = JSON.parse(ajax.responseText);
            if(response == "No"){
              document.getElementById('contentErrorModalInicio').innerHTML = "Usuario o contraseña incorrectos";
              document.getElementById('botoErrorInicioSesion').click();
            }else{
              if(localStorage.getItem('recordar')){
                localStorage.setItem('usuario', objeto.usuario);
                localStorage.setItem('password', objeto.password);
              }
              localStorage.setItem('nombre', response);
              window.location.href = "https://www.desposte.tk/solicitud";
            }
            /**/
          }
      }
      ajax.send();
    }
    
