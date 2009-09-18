<?php

class dmUserLogActions extends dmAdminBaseActions
{
  
  public function preExecute()
  {
    $this->log = $this->dmContext->getService('user_log');
  }
  
  public function executeClear(dmWebRequest $request)
  {
    $this->log->clear();
    $this->getUser()->logInfo(dm::getI18n()->__('User log cleared'));
    return $this->redirect('dmUserLog/index');
  }
  
  public function executeRefresh(dmWebRequest $request)
  {
    $responseHash = md5(dmArray::first($this->log->getEntries(1))->toJson());
    
    if ($responseHash == $request->getParameter('hash'))
    {
      return $this->renderText('-');
    }
    
    $viewClass = 'dmUserLogView'.dmString::camelize($request->getParameter('view'));
    
    $view = new $viewClass($this->log, $this->getUser()->getCulture());
    
    return $this->renderText(
      $view->renderBody($request->getParameter('max', 20)).
      '__DM_SPLIT__'.
      $responseHash
    );
  }
  
  public function executeIndex(dmWebRequest $request)
  {
    $this->view = new dmUserLogView($this->log, $this->getUser()->getCulture());
    $this->filesize = $this->log->getSize();
  }
  
}