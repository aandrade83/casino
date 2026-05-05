<? include($_SERVER['DOCUMENT_ROOT']."/utilities/includes.php");  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Casino - Panel de Control</title>
<link href="../utilities/css/admin/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../utilities/js/validate.js"></script>

</head>

<body>

  <hgroup>
  
  <p><img src="../utilities/images/logo.png" width="712" height="123" alt="HTML5 Casino" /></p>
  
  <h1>Control Panel Login</h1>
  <? if(param("fail")){ ?> <p class="error">Invalid User or Password</p>
  <? }else if(param("blocked")){ ?> <p class="error">You have reached the maximum login attempts, please try again in 1 hour</p>
  <? }else if(param("expired")){ ?> <p class="error">Session expired, please login again</p> <? } ?>
</hgroup>
<script type="text/javascript">
var validations = new Array();
validations.push({id:"user",type:"null", msg:"Insert your user"});
validations.push({id:"password",type:"null", msg:"Insert your password"});
</script>
<form action="../utilities/process/admin_login.php" method="post" onsubmit="return validate(validations);">
  <div class="group">
    <input type="text" name="user" id="user" placeholder="User">
  </div>
  <div class="group">
    <input type="password" name="password" id="password" placeholder="Password">
  </div>
  <input name="Enviar" type="submit" class="button buttonBlue" value="Login">
</form>
<footer>
  <p>HTML5 Casino Administrator</p>
</footer>

</body>

</html>