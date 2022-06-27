<?php


namespace App\Channels\Messages;

class WhatsAppTemplate
{
  public $templateName;
  public $type = 'message_template';
  public $image;
  public $params = [];
  public $parameters = [];

  public function templateName($templateName)
  {
    $this->templateName = $templateName;

    return $this;
  }
  
  public function type($type)
  {
    $this->type = $type;

    return $this;
  }
  
  public function image($image)
  {
    $this->image = $image;

    return $this;
  }

  public function default($default)
  {
    return $this->withDefault($default);
  }

  public function withDefault($default)
  {
    $this->params[] = ["default" => $default];

    return $this;
  }

  public function text($text)
  {
    return $this->withText($text);
  }

  public function withText($text)
  {
    $this->parameters[] = ["type" => "text", "text" => $text];

    return $this;
  }

}