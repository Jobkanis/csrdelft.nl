<?php
/**
 * The ${NAME} file.
 */

namespace CsrDelft\model\groepen\leden;

use CsrDelft\model\AbstractGroepLedenModel;
use CsrDelft\model\entity\groepen\KetzerDeelnemer;

class KetzerDeelnemersModel extends AbstractGroepLedenModel
{

    const ORM = KetzerDeelnemer::class;

    protected static $instance;

}