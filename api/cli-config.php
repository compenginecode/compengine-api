<?php

/** cli-config.php
 *
 *  This file is used by the Doctrine command line tool. Do not edit or delete.
 *  In order to update the schema, run:
 *      $ "vendor/bin/doctrine" orm:schema-tool:update
 *
 * @author A. I. Grayson-Widarsito
 * @date 2016
 *
 * */

require_once "source/bootstrap.php";

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);