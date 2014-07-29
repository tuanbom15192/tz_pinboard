<?php
/*------------------------------------------------------------------------

# TZ Pinboard Extension

# ------------------------------------------------------------------------

# author    TuNguyenTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Articles list controller class.
 */
class TZ_PinboardControllerArticles extends JControllerAdmin
{
    /**
     * Constructor.
     *
     * @param    array $config An optional associative array of configuration settings.
     * @return    ContentControllerArticles
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        JFactory::getLanguage()->load('com_content');
        // Articles default form can come from the articles or featured view.
        // Adjust the redirect view on the value of 'view' in the request.
        if (JRequest::getCmd('view') == 'featured') {
            $this->view_list = 'featured';
        }
        parent::__construct($config);

        $this->registerTask('unfeatured', 'featured');
    }

    /**
     * Method to publish a list of items
     *
     * @return  void
     *
     * @since   11.1
     */
    public function publish()
    {
        // Check for request forgeries
        JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = JRequest::getVar('cid', array(), '', 'array');
        $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid)) {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);
            if (count($cid) <= 1) {
                $text_publish = '_N_PIN_PUBLISHED';
                $text_unpublish = '_N_PIN_UNPUBLISHED';
                $text_archived = '_N_PIN_ARCHIVED';
                $text_trashed = '_N_PIN_TRASHED';
            } else {
                $text_publish = '_N_PINS_PUBLISHED';
                $text_unpublish = '_N_PINS_UNPUBLISHED';
                $text_archived = '_N_PINS_ARCHIVED';
                $text_trashed = '_N_PINS_TRASHED';
            }
            // Publish the items.
            if (!$model->publish($cid, $value)) {
                JError::raiseWarning(500, $model->getError());
            } else {
                if ($value == 1) {
                    $ntext = $this->text_prefix . $text_publish;
                } elseif ($value == 0) {
                    $ntext = $this->text_prefix . $text_unpublish;
                } elseif ($value == 2) {
                    $ntext = $this->text_prefix . $text_archived;
                } else {
                    $ntext = $this->text_prefix . $text_trashed;
                }
                $this->setMessage(JText::plural($ntext, count($cid)));
            }
        }
        $extension = JRequest::getCmd('extension');
        $extensionURL = ($extension) ? '&extension=' . JRequest::getCmd('extension') : '';
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
    }

    public function delete()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1) {
            JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);
            if (count($cid) <= 1) {
                $text = '_N_PIN_DELETED';
            } else {
                $text = '_N_PINS_DELETED';
            }
            // Remove the items.
            if ($model->delete($cid)) {
                $this->setMessage(JText::plural($this->text_prefix . $text, count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }
        // Invoke the postDelete method to allow for the child class to access the model.

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }

    /*
         * Proxy for getModel.
         *
         * @param    string $name The name of the model.
         * @param    string $prefix The prefix for the PHP class name.
         *
         * @return    JModel
         * @since    1.6
         */
    public function getModel($name = 'Article', $prefix = 'TZ_PinboardModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
