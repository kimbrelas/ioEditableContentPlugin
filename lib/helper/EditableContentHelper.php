<?php

/**
 * Helper class for the editable content plugin.
 * 
 * Assists in outputting the editable content tags
 * 
 * @package     ioEditableContentPlugin
 * @subpackage  helper
 * @author      Ryan Weaver <ryan.weaver@iostudio.com>
 */


/**
 * Returns an editable field with markup necessary to allow editing
 *
 * All options are rendered as attributes, except for these special options:
 *   * partial - A partial to render with instead of using the raw content value
 *   * form    - A form class to use for editing the content. The form class
 *               will be stripped to only include the given content. To
 *               edit entire forms, use get_editable_form().
 *   * form_partial - The partial used to render the fields of the form
 *   * mode    - Which type of editor to load: fancybox(default)|inline
 *
 * @example
 * editable_content_tag('div', $blog, 'title');
 *
 * editable_content_tag('div', $blog, null, array(
 *   'partial'  => 'blog/show',
 *   'form'     => 'myBlogForm',
 *   'form_partial' => 'blog/form',
 *   'mode'     => 'inline',
 *   'class'    => 'my_div_class', // output as an attribute on the div wrapper
 * ));
 *
 * @param string  $tag      The tag to render (e.g. div, a, span)
 * @param mixed   $obj      A Doctrine/Propel record
 * @param string  $field    The name of the field to edit & render
 * @param array   $options  Mixture of options and attributes (see above)
 *
 * @return string
 */
function editable_content_tag($tag, $obj, $field, $options = array())
{
  $user = sfContext::getInstance()->getUser(); // -1 kitten

  return get_editable_content_service()->getEditableContentTag($tag, $obj, $field, $options, $user);
}

/**
 * Shortcut to return the current editable content service.
 *
 * @return ioEditableContentService
 */
function get_editable_content_service()
{
  return sfApplicationConfiguration::getActive()
    ->getPluginConfiguration('ioEditableContentPlugin')
    ->getEditableContentService();
}