<?php
 // no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin'); 

class plgSystemSCHidden extends JPlugin
{

	public function __construct(&$subject, $config){
		parent::__construct($subject, $config);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function onAfterRender()
	{
		$this->ButtonShortcodes();  //add shortcode to editor
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    protected $autoloadLanguage = true ;	
	function onContentPrepare($context, $article, $params, $limitstart)
     {   
      // check user type 	
      $user = JFactory::getUser();
      $groups= $user->getAuthorisedGroups();
      $curentUser=$groups[1];  // get group id by current user
          
      $regex='/\[SC id\=\"[0-9]\"\]/s';
      preg_match($regex,$article->text, $matches);  
      if(isset($matches[0]))
        {
		 $host = $matches[0];
			
            
         if (isset($host))
            {      
              preg_match('@\d+@', $host, $matches);
              $id= $matches[0];  // get group id given by user
		              
           
              if (($curentUser == $id or $curentUser == 8) or ($curentUser != 9 and $id == 2))  // show content
                {
					$patterns = array();
					$patterns[0] = '/\[SC id\=\"(.*)\"\]/';
					$patterns[1] = '/\[\/SC\]/';
					$replacements = array();
					$replacements[0] = '';
					$replacements[1] = '';
					$article->text=  preg_replace($patterns,$replacements,$article->text);
					
			     }
		      else  //hide content
			     {
					$newurl = '<a href="http://'.$this->params->get('addr_link').'" target="_blank">'.$this->params->get('name_link')."</a>";
                    $pattern = '/\[SC id\=\"[0-9]\"\](.*?)\[\/SC\]/s';
                    $article->text=  preg_replace($pattern,$newurl,$article->text);    
			     }
            }	  
        } 
     }	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function ButtonShortcodes()
	{
		$Scpage   = JResponse::GetBody();
		$btn = $this->valueShortcode();
		$sSTR  = '<script  type="text/javascript">		        
						function getInputUser(msg) {    
							txt = prompt(msg);
			                if (!txt) 
			                {
			                return;
						    }
						    else
						    {
						     STR="<br/><br/>[SC id=\'9\']<br/>"+txt+"<br/>[/SC]<br/>";
						    }
							STR = STR.replace(/\'/g, \'"\');

							if(document.getElementById(\'jform_articletext\') != null) {
								jInsertEditorText(STR, \'jform_articletext\');
								
							}				
						}
				   </script>';
		$Scpage = str_replace('<div id="editor-xtd-buttons">', '<div id="editor-xtd-buttons">' . $btn, $Scpage);
		$Scpage = str_replace('<div id="editor-xtd-buttons" class="btn-toolbar pull-left">', '<div id="editor-xtd-buttons" class="btn-toolbar pull-left">' . $btn, $Scpage);
		$Scpage = str_replace('</body>', $sSTR . '</body>', $Scpage);
		JResponse::SetBody($Scpage);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function valueShortcode()
	{
		$linksc = array('name'		=> "Link",'tooltip'		=> "Link");		
		$STR  = '';
		$msg=$this->params->get('msg');
		$STR .= '<a class="jomjome" href="javascript: void(0);" onclick="getInputUser(\''.$msg.'\')" title="' . $linksc['tooltip'] . '">'; 
		$STR .='<div>';
		$STR .='<span class="btn">add SCHidden</span>';
	    $STR .= '</a>';
		$STR .= '</li>';
		$STR .='</div>';
		
		return $STR; 
		
	}
}
