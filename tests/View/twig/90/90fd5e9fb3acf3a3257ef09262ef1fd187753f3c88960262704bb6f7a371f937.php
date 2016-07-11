<?php

/* twigtemplate.twig.php */
class __TwigTemplate_880c564a915511ca78791d8dc6f17882fe83bc5895a71d2e110d9a590252952d extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "Hello, This is a ";
        echo twig_escape_filter($this->env, (isset($context["name"]) ? $context["name"] : null), "html", null, true);
        echo ". user.name=";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "name", array()), "html", null, true);
        echo ", enjoy!
";
    }

    public function getTemplateName()
    {
        return "twigtemplate.twig.php";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
/* Hello, This is a {{name}}. user.name={{user.name}}, enjoy!*/
/* */
