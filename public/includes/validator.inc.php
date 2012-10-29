<?php

function check_email($email) {
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    return false;
  }
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
    if
(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
$local_array[$i])) {
      return false;
    }
  }
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false;
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if
(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
↪([A-Za-z0-9]+))$",
$domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}

function check_username($username) {
	if (isset($username[24]))
		return false;
	if (!isset($username[2]))
		return false;
	return ctype_alpha($username);
}

function check_required($var) {
	if (!isset($var[0]))
		return false;
	return true;
}

function validate_ext($file_name,$type = "any") {
	if ($type == "invoice")
    	$ext_array = array(".doc",".docx",".pages",".pdf",".rtf",".txt",".html");
    $extension = strtolower(strrchr($file_name,"."));
    $ext_count = count($ext_array);

    if (!$file_name) {
        return false;
    } else {
        if ($extension==".php") {
        	return false;
        } elseif (!$ext_array) {
            return true;
        } else {
            foreach ($ext_array as $value) {
                $first_char = substr($value,0,1);
                    if ($first_char <> ".") {
                        $extensions[] = ".".strtolower($value);
                    } else {
                        $extensions[] = strtolower($value);
                    }
            }

            foreach ($extensions as $value) {
                if ($value == $extension) {
                    $valid_extension = "TRUE";
                }
            }

            if ($valid_extension) {
                return true;
            } else {
                return false;
            }
        }
    }
}

?>