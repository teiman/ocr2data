<?PHP
/**
 * patTemplate modfifier Truncate
 *
 * $Id: modifiers.xml 34 2004-05-11 19:46:09Z schst $
 *
 * @package        patTemplate
 * @subpackage     Modifiers
 * @author
 */
class patTemplate_Modifier_Html8 extends patTemplate_Modifier
{
   /**
    * truncate the string
    *
    * @access    public
    * @param    string        value
    * @return    string       modified value
    */
    function modify( $value, $params = array() )  {
        return  htmlentities($value,ENT_QUOTES,'UTF-8');
		//return  htmlentities($value,ENT_QUOTES) ."lalala";
    }


}


?>