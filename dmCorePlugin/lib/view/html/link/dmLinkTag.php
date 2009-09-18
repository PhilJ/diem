<?php

abstract class dmLinkTag extends dmHtmlTag
{
  protected
  $attributesToRemove      = array('text', 'source'),
  $emptyAttributesToRemove = array('class', 'target', 'title');

  /*
   * @return string baseHref the href without query string
   */
  abstract protected function getBaseHref();

  protected function configure()
  {
  }

  /*
   * Set text
   * @return dmLinkTag $this
   */
  public function text($v)
  {
    return $this->set('text', (string) $v);
  }

  /*
   * Set title
   * @return dmLinkTag $this
   */
  public function title($v)
  {
    return $this->set('title', (string) $v);
  }

  /*
   * Set text and title
   * @return dmLinkTag $this
   */
  public function textTitle($v)
  {
    return $this->text($v)->title($v);
  }

  /*
   * Transform into rss link
   * @return dmLinkTag $this
   */
  public function rss($v)
  {
    return $this->set('rss', (bool) $v);
  }

  /*
   * Shortcut to ->target('blank')
   * @return dmLinkTag $this
   */
  public function blank($v)
  {
    return $this->target($v ? '_blank' : null);
  }

  /*
   * Set link target
   * @return dmLinkTag $this
   */
  public function target($v)
  {
    if (in_array($v, array('blank', 'parent', 'self', 'top')))
    {
      $v = '_'.$v;
    }

    return $this->set('target', $v);
  }

  /*
   * Add an anchor
   * @return dmLinkTag $this
   */
  public function anchor($v)
  {
    return $this->set('anchor', trim((string) $anchor, '#'));
  }

  /*
   * Add a request parameter
   * @return dmLinkTag $this
   */
  public function param($key, $value)
  {
    return $this->params(array($key => $value));
  }

  /*
   * Add request parameters
   * @return dmLinkTag $this
   */
  public function params(array $params)
  {
    foreach($params as $key => $value)
    {
      $params[$key] = urlencode($value);
    }

    return $this->set('params', array_merge($this->get('params', array()), $params));
  }

  public function render()
  {
    return '<a'.$this->getHtmlAttributes().'>'.$this->renderText().'</a>';
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    $href = $this->getBaseHref();

    if (isset($attributes['params']))
    {
      if (!empty($attributes['params']))
      {
        $href = $this->buildUrl(
        self::getBaseFromUrl($href),
        array_merge(self::getDataFromUrl($href), $this->options['params'])
        );
      }
      unset($attributes['params']);
    }

    $attributes['href'] = $href;

    return $attributes;
  }

  public function getHref()
  {
    return dmArray::get($this->prepareAttributesForHtml($this->options), 'href');
  }

  public function getAbsoluteHref()
  {
    $href = $this->getHref();
    
    $uriPrefix = dm::getRequest()->getUriPrefix();
     
    if (strpos($href, $uriPrefix) !== 0)
    {
      $href = $uriPrefix.$href;
    }
     
    return $href;
  }

  protected function renderText()
  {
    return $this->options['text'];
  }

  protected static function getBaseFromUrl($url)
  {
    if ($pos = strpos($url, '?'))
    {
      return substr($url, 0, $pos);
    }

    return $url;
  }

  protected static function getDataFromUrl($url)
  {
    if ($pos = strpos($url, '?'))
    {
      parse_str(substr($url, $pos + 1), $params);
      return $params;
    }

    return array();
  }

  protected function buildUrl($base, array $data = array())
  {
    return $base.'?'.http_build_query($data);
  }
  
}