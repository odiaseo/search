<?php header('Access-Control-Allow-Origin: *'); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Maple Syrup Q Platform</title>

    <link href='//fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'/>
    <link href='/components/swagger-ui/dist/css/typography.css' media='screen' rel='stylesheet' type='text/css'/>
    <link href='/components/swagger-ui/dist/css/reset.css' media='screen' rel='stylesheet' type='text/css'/>
    <link href='/components/swagger-ui/dist/css/screen.css' media='screen' rel='stylesheet' type='text/css'/>
    <link href='/components/swagger-ui/dist/css/reset.css' media='print' rel='stylesheet' type='text/css'/>
    <link href='/components/swagger-ui/dist/css/print.css' media='print' rel='stylesheet' type='text/css'/>
    <script src='/components/swagger-ui/dist/lib/jquery-1.8.0.min.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/jquery.slideto.min.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/jquery.wiggle.min.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/jquery.ba-bbq.min.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/handlebars-2.0.0.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/underscore-min.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/backbone-min.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/swagger-ui.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/highlight.7.3.pack.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/marked.js' type='text/javascript'></script>
    <script src='/components/swagger-ui/dist/lib/swagger-oauth.js' type='text/javascript'></script>

    <!-- enabling this will enable oauth2 implicit scope support -->
    <script src='/components/swagger-ui/dist/lib/swagger-oauth.js' type='text/javascript'></script>

  <script type="text/javascript">
    $(function () {
      window.swaggerUi = new SwaggerUi({
      url: "<?=$urlToDocs?>",
      dom_id: "swagger-ui-container",
      supportedSubmitMethods: ['get', 'post', 'put', 'delete' , 'patch'],
      onComplete: function(swaggerApi, swaggerUi){
        console.log("Loaded SwaggerUI");

        if(typeof initOAuth == "function") {
          /*
          initOAuth({
            clientId: "your-client-id",
            realm: "your-realms",
            appName: "your-app-name"
          });
          */
        }
        $('pre code').each(function(i, e) {
          hljs.highlightBlock(e)
        });
      },
      onFailure: function(data) {
          console.log("Unable to Load SwaggerUI");
      },
      docExpansion: "none",
      sorter : "alpha"
    });

    $('#input_apiKey').change(function() {
      var key = $('#input_apiKey')[0].value;
        console.log("key: " + key);
      if(key && key.trim() != "") {
          console.log("added key " + key);
        window.authorizations.add("key", new ApiKeyAuthorization("api_key", key, "query"));
      }
    })
    window.swaggerUi.load();
  });
  </script>
</head>

<body class="swagger-section">
<div id='header'>
  <div class="swagger-ui-wrap">
    <a id="logo" href="<?=url(config('app.api_docs'));?>">Q Platform</a>
    <form id='api_selector'>
<!--      <div class='input icon-btn'>-->
<!--        <img id="show-pet-store-icon" src="images/pet_store_api.png" title="Show Swagger Petstore Example Apis">-->
<!--      </div>-->
<!--      <div class='input icon-btn'>-->
<!--        <img id="show-wordnik-dev-icon" src="images/wordnik_api.png" title="Show Wordnik Developer Apis">-->
<!--      </div>-->
      <div class='input'><input placeholder="<?=url(config('app.swagger_doc_route'));?>" id="input_baseUrl" name="baseUrl" type="text"/></div>
      <div class='input'><input placeholder="api_key" id="input_apiKey" name="apiKey" type="text"/></div>
      <div class='input'><a id="explore" href="#">Explore</a></div>
    </form>
  </div>
</div>

<div id="message-bar" class="swagger-ui-wrap">&nbsp;</div>
<div id="swagger-ui-container" class="swagger-ui-wrap"></div>
</body>
</html>
