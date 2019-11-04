<?php

namespace InfrastructureLayer\EmailTemplate;

/**
 * Class EmailTemplate
 * @package InfrastructureLayer\Email\EmailTemplate
 */
class EmailTemplate
{
    /** generateTemplate
     *
     *
     *
     * @param $templatePath
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function generateTemplate($templatePath, $args = []) {
        if (!file_exists($templatePath)){
            throw new \Exception("Template at " . $templatePath . " was not found!");
        }

        $loader = new \Twig_Loader_Array(array(
            "template" => file_get_contents($templatePath)
        ));
        $twig = new \Twig_Environment($loader);

        $render = $twig->render("template", $args);

        return $render;
    }
}
