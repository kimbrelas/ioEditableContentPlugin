<?php

/**
 * Actions class for editable content
 * 
 * @package     ioEditableContentPlugin
 * @subpackage  actions
 * @author      Ryan Weaver <ryan.weaver@iostudio.com>
 */
class BaseioEditableContentActions extends sfActions
{
  public function preExecute()
  {
    $this->pluginWebRoot = sfConfig::get('app_editable_content_assets_web_root', '/ioEditableContentPlugin');
    $this->editableClassName = $this->getEditableContentService()
      ->getOption('editable_class_name', 'io_editable_content');
  }

  // the dynamic css file
  public function executeCss(sfWebRequest $request)
  {
    $this->_checkCredentials();
  }

  // the dynamic js file
  public function executeJs(sfWebRequest $request)
  {
    $this->_checkCredentials();
  }

  /**
   * Action that renders a particular inline edit form
   */
  public function executeForm(sfWebRequest $request)
  {
    $this->_setupVariables($request);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->_setupVariables($request);
    $this->form->bind($request->getParameter($this->form->getName()));

    $json = array();
    if ($this->form->isValid())
    {
      $json['error'] = false;
      $this->form->save();
    }
    else
    {
      $json['error'] = sprintf(
        'There were %s errors when submitting the form.',
        count($this->form->getErrorSchema()->getErrors())
      );
    }

    $formPartial = $request->getParameter('form_partial', 'ioEditableContent/formFields');
    
    $json['response'] = $this->getPartial($formPartial);
    $this->renderText(json_encode($json));

    return sfView::NONE;
  }


  /**
   * Returns the form object based on the request parameters
   *
   * @param sfWebRequest $request
   * @return sfForm
   */
  protected function _setupVariables(sfWebRequest $request)
  {
    $this->model = $request->getParameter('model');
    $this->pk = $request->getParameter('pk');

    $this->formClass = $request->getParameter('form', $this->model.'Form');
    $this->formPartial = $request->getParameter('form_partial', 'ioEditableContent/formFields');
    $this->fields = $request->getParameter('fields');

    $this->partial = $request->getParameter('partial');

    // @todo make this work with propel
    $this->forward404Unless($this->model && $this->pk);
    $object = Doctrine_Core::getTable($this->model)->find($this->pk);
    $this->forward404Unless($object);

    if (!class_exists($this->formClass))
    {
      $this->renderText(sprintf('<div>Cannot find form class "%s"</div>', $this->formClass));
      return sfView::NONE;
    }

    $this->form = new $this->formClass($object);
    if ($this->fields)
    {
      $this->form->useFields($this->fields);
    }
  }


  /**
   * Helper to forward 404 if the user doesn't have edit credentials
   */
  protected function _checkCredentials()
  {
    $this->forward404Unless($this->getEditableContentService()->shouldShowEditor($this->getUser()));
  }

  /**
   * @return ioEditableContentService
   */
  protected function getEditableContentService()
  {
    return $this->getContext()
      ->getConfiguration()
      ->getPluginConfiguration('ioEditableContentPlugin')
      ->getEditableContentService();
  }  
}