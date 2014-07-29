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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.folder');

require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_tz_pinboard' . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'pins.php');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/content.php';
require_once(JPATH_LIBRARIES . '/joomla/filesystem/file.php');
define('TZ_IMAGE_SIZE', 10 * 1024 * 1024);

/**
 * Item Model for an Article.
 */
class TZ_PinboardModelArticle extends JModelAdmin
{
    /**
     * @var        string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $text_prefix = 'COM_CONTENT';
    private $fieldsid = array();
    private $imageUrl = 'media/tz_pinboard/article';
    private $tzfolder = 'tz_pinboard';
    private $attachUrl = 'attachments';
    private $contentid = null;
    private $catParams = null;

    function __construct()
    {
        $this->contentid = JRequest::getCmd('id');
        parent::__construct();
    }

    protected function populateState()
    {
        parent::populateState();
        $params = JComponentHelper::getParams('com_tz_pinboard');

        $this->setState('params', $params);

        if ($params->get('tz_image_xsmall', 100)) {
            $sizeImage['XS'] = (int)$params->get('tz_image_xsmall', 100);
        }
        if ($params->get('tz_image_small', 200)) {
            $sizeImage['S'] = (int)$params->get('tz_image_small', 200);
        }
        if ($params->get('tz_image_medium', 400)) {
            $sizeImage['M'] = (int)$params->get('tz_image_medium', 400);
        }
        if ($params->get('tz_image_large', 600)) {
            $sizeImage['L'] = (int)$params->get('tz_image_large', 600);
        }
        if ($params->get('tz_image_xsmall', 900)) {
            $sizeImage['XL'] = (int)$params->get('tz_image_xlarge', 900);
        }
        $this->setState('sizeImage', $sizeImage);
        $this->setState('article.id', JRequest::getInt('id'));
    }

    /**
     * Batch copy items to a new category or current.
     *
     * @param   integer $value The new category.
     * @param   array $pks An array of row IDs.
     * @param   array $contexts An array of item contexts.
     *
     * @return  mixed  An array of new IDs on success, boolean false on failure.
     *
     * @since    11.1
     */
    protected function batchCopy($value, $pks, $contexts)
    {

        $categoryId = (int)$value;

        $table = $this->getTable();
        $i = 0;

        // Check that the category exists
        if ($categoryId) {
            $categoryTable = JTable::getInstance('Category');
            if (!$categoryTable->load($categoryId)) {
                if ($error = $categoryTable->getError()) {
                    // Fatal error
                    $this->setError($error);
                    return false;
                } else {
                    $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
                    return false;
                }
            }
        }

        if (empty($categoryId)) {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
            return false;
        }

        // Check that the user has create permission for the component
        $extension = JFactory::getApplication()->input->get('option', '');
        $user = JFactory::getUser();
        if (!$user->authorise('core.create', $extension . '.category.' . $categoryId)) {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
            return false;
        }

        // Parent exists so we let's proceed
        while (!empty($pks)) {
            // Pop the first ID off the stack
            $pk = array_shift($pks);

            $table->reset();

            // Check that the row actually exists
            if (!$table->load($pk)) {
                if ($error = $table->getError()) {
                    // Fatal error
                    $this->setError($error);
                    return false;
                } else {
                    // Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            // Alter the title & alias
            $data = $this->generateNewTitle($categoryId, $table->alias, $table->title);
            $table->title = $data['0'];
            $table->alias = $data['1'];

            // Reset the ID because we are making a copy
            $table->id = 0;

            // New category ID
            $table->catid = $categoryId;

            // TODO: Deal with ordering?
            //$table->ordering	= 1;

            // Get the featured state
            $featured = $table->featured;

            // Check the row.
            if (!$table->check()) {
                $this->setError($table->getError());
                return false;
            }

            // Store the row.
            if (!$table->store()) {
                $this->setError($table->getError());
                return false;
            }

            // Get the new item ID
            $newId = $table->get('id');

            $db = JFactory::getDbo();
            // Store new article to table tz_pinboard
            $query2 = 'SELECT * FROM #__tz_pinboard'
                . ' WHERE contentid=' . $pk;
            $db->setQuery($query2);
            if (!$db->query()) {
                $this->setError($db->getErrorMsg());
                return false;
            }

            $val = array();
            $rows = $db->loadObjectList();
            foreach ($rows as $row) {
                //Copy image fields
                $imageName = '';
                if (!empty($row->images)) {
                    $imageName = uniqid() . 'tz_pinboard_' . time() . '.' . JFile::getExt($row->images);
                    $subUrl = substr($row->images, 0, strrpos($row->images, '/'));
                    $path = JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subUrl) . DIRECTORY_SEPARATOR . $imageName;
                    if (JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $row->images))) {
                        JFile::copy(JPATH_SITE . DIRECTORY_SEPARATOR . $row->images, $path);
                    }
                }
                $val[] = '(' . $newId . ',' . $row->fieldsid . ',"' . $row->value . '","' . $subUrl . '/' . $imageName . '","' . $row->imagetitle . '")';
            }
            if (count($val) > 0) {
                $val = implode(',', $val);

                $query2 = 'INSERT INTO #__tz_pinboard(`contentid`,`fieldsid`,`value`,`images`,`imagetitle`)'
                    . ' VALUES ' . $val;
                $db->setQuery($query2);
                if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
            }


            // Store new ariticle to table tz_pinboard_xref_content
            $query2 = 'SELECT * FROM #__tz_pinboard_xref_content'
                . ' WHERE contentid=' . $pk;
            $db->setQuery($query2);
            if (!$db->query()) {
                $this->setError($db->getErrorMsg());
                return false;
            }

            $values = null;
            $attachFiles = null;
            $attachTitle = null;

            $rows = $db->loadObjectList();
            foreach ($rows as $row) {
                //Copy attachment
                if (!empty($row->attachfiles)) {
                    $attachFileName = explode('///', $row->attachfiles);
                    $attachTitle = $row->attachtitle;
                    $attachmentsOld = $row->attachold;
                    $i = 0;
                    foreach ($attachFileName as $item) {
                        $fileName = 'tz_pinboard_' . (time() + $i)
                            . '.' . JFile::getExt($item);
                        $srcPath = JPATH_SITE . DIRECTORY_SEPARATOR . 'media'
                            . DIRECTORY_SEPARATOR . $item;
                        $desPath = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR
                            . $this->tzfolder
                            . DIRECTORY_SEPARATOR . $this->attachUrl
                            . DIRECTORY_SEPARATOR . $fileName;

                        $_fileName = explode('/', $item);
                        if (JFile::exists($srcPath)) {
                            $attachFiles[$i] = $this->tzfolder . '/'
                                . $this->attachUrl . '/'
                                . $fileName;
                            JFile::copy($srcPath, $desPath);
                        }

                        $i++;
                    }
                }
                if (is_array($attachFiles) && count($attachFiles) > 0) {
                    $attachFiles = implode('///', $attachFiles);
                }
                //end attachment

                $sizes = $this->getState('size');
                $imageName = '';
                $imageHoverName = '';
                $galleryName = '';
                $videoThumb = '';
                //Copy Image
                if (!empty($row->images)) {
                    $imageName = uniqid() . 'tz_pinboard_' . time() . '.' . JFile::getExt($row->images);
                    $subUrl = substr($row->images, 0, strrpos($row->images, '/'));
                    $imageName = $subUrl . '/' . $imageName;

                    foreach ($sizes as $key => $val) {
                        $newImage = str_replace('/', DIRECTORY_SEPARATOR, $imageName);
                        $newImage = str_replace('.' . JFile::getExt($row->images),
                            '_' . $key . '.' . JFile::getExt($row->images), $newImage);
                        $path = JPATH_SITE . DIRECTORY_SEPARATOR . $newImage;

                        $srcName = str_replace('/', DIRECTORY_SEPARATOR, $row->images);
                        $srcName = str_replace('.' . JFile::getExt($srcName),
                            '_' . $key . '.' . JFile::getExt($srcName), $srcName);
                        $srcPath2 = JPATH_SITE . DIRECTORY_SEPARATOR . $srcName;
                        if (JFile::exists($srcPath2)) {
                            JFile::copy($srcPath2, $path);
                        }

                    }
                }
                //end Image
                //Copy Image Hover
                if (!empty($row->images_hover)) {
                    $imageHoverName = uniqid() . 'tz_pinboard_' . time() . '.' . JFile::getExt($row->images_hover);
                    $subUrl = substr($row->images_hover, 0, strrpos($row->images_hover, '/'));
                    $imageHoverName = $subUrl . '/' . $imageHoverName;

                    foreach ($sizes as $key => $val) {
                        $newImageHover = str_replace('/', DIRECTORY_SEPARATOR, $imageHoverName);
                        $newImageHover = str_replace('.' . JFile::getExt($row->images_hover),
                            '_' . $key . '.' . JFile::getExt($row->images_hover), $newImageHover);
                        $path = JPATH_SITE . DIRECTORY_SEPARATOR . $newImageHover;

                        $srcName = str_replace('/', DIRECTORY_SEPARATOR, $row->images_hover);
                        $srcName = str_replace('.' . JFile::getExt($srcName),
                            '_' . $key . '.' . JFile::getExt($srcName), $srcName);
                        $srcPath2 = JPATH_SITE . DIRECTORY_SEPARATOR . $srcName;
                        if (JFile::exists($srcPath2)) {
                            JFile::copy($srcPath2, $path);
                        }

                    }
                }
                //end Image Hover
                //Copy gallery
                if (!empty($row->gallery)) {
                    $arr = explode('///', $row->gallery);
                    foreach ($arr as $gallery) {
                        $str = uniqid() . 'tz_pinboard_' . time() . '.' . JFile::getExt($gallery);
                        $subUrl = substr($gallery, 0, strrpos($gallery, '/'));
                        $galleryName[] = $subUrl . '/' . $str;

                        foreach ($sizes as $key => $val) {
                            $newGallery = str_replace('/', DIRECTORY_SEPARATOR, $subUrl . '/' . $str);
                            $newGallery = str_replace('.' . JFile::getExt($newGallery),
                                '_' . $key . '.' . JFile::getExt($gallery), $newGallery);
                            $path = JPATH_SITE . DIRECTORY_SEPARATOR . $newGallery;

                            $srcName = str_replace('/', DIRECTORY_SEPARATOR, $gallery);
                            $srcName = str_replace('.' . JFile::getExt($srcName),
                                '_' . $key . '.' . JFile::getExt($srcName), $srcName);
                            $srcPath2 = JPATH_SITE . DIRECTORY_SEPARATOR . $srcName;
                            if (JFile::exists($srcPath2)) {
                                JFile::copy($srcPath2, $path);
                            }
                        }
                        if (JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $gallery))) {
                            JFile::copy(JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $gallery), $path);
                        }
                    }
                    if (count($galleryName) > 0) {
                        $galleryName = implode('///', $galleryName);
                    }
                }
                //end Gallery
                //Copy video thumbnail
                if (!empty($row->videothumb)) {
                    $videoThumb = uniqid() . 'tz_pinboard_' . time() . '.' . JFile::getExt($gallery);
                    $subUrl = substr($row->videothumb, 0, strrpos($row->videothumb, '/'));
                    $videoThumb = $subUrl . '/' . $videoThumb;

                    foreach ($sizes as $key => $val) {
                        $newThumb = str_replace('/', DIRECTORY_SEPARATOR, $videoThumb);
                        $newThumb = str_replace('.' . JFile::getExt($newThumb),
                            '_' . $key . '.' . JFile::getExt($newThumb), $newThumb);
                        $path = JPATH_SITE . DIRECTORY_SEPARATOR . $newThumb;

                        $srcName = str_replace('/', DIRECTORY_SEPARATOR, $row->videothumb);
                        $srcName = str_replace('.' . JFile::getExt($srcName),
                            '_' . $key . '.' . JFile::getExt($srcName), $srcName);
                        $srcPath2 = JPATH_SITE . DIRECTORY_SEPARATOR . $srcName;
                        if (JFile::exists($srcPath2)) {
                            JFile::copy($srcPath2, $path);
                        }
                    }
                }
                //end video thumbnail

                $values[] = '(' . $newId . ',' . $row->groupid . ',"'
                    . $imageName . '","'
                    . $row->imagetitle . '","' . $imageHoverName . '","'
                    . $attachFiles . '","'
                    . $attachTitle . '","' . $attachmentsOld . '","'
                    . $galleryName . '","' . $row->gallerytitle . '","'
                    . $row->video . '","' . $row->videotitle . '","' . $videoThumb . '","' . $row->type . '")';
            }
            if (count($values) > 0) {
                $values = implode(',', $values);
                $query2 = 'INSERT INTO #__tz_pinboard_xref_content(`contentid`,`groupid`,`images`,`imagetitle`,'
                    . '`images_hover`,`attachfiles`,`attachtitle`,`attachold`,`gallery`,`gallerytitle`,`video`,'
                    . '`videotitle`,`videothumb`,`type`)'
                    . ' VALUES ' . $values;
                $db->setQuery($query2);
                if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
            }


            // Add the new ID to the array
            $newIds[$i] = $newId;
            $i++;

            // Check if the article was featured and update the #__content_frontpage table
            if ($featured == 1) {
                $db = $this->getDbo();
                $query = $db->getQuery(true);
                $query->insert($db->quoteName('#__content_frontpage'));
                $query->values($newId . ', 0');
                $db->setQuery($query);
                $db->query();
            }

        }
        // Clean the cache
        $this->cleanCache();

        return $newIds;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param    object $record A record object.
     *
     * @return    boolean    True if allowed to delete the record. Defaults to the permission set in the component.
     * @since    1.6
     */
    protected function canDelete($record)
    {
        if (!empty($record->id)) {
            if ($record->state != -2) {
                return;
            }
            $user = JFactory::getUser();
            return $user->authorise('core.delete', 'com_content.article.' . (int)$record->id);
        }
    }

    /**
     * Method to test whether a record can have its state edited.
     *
     * @param    object $record A record object.
     *
     * @return    boolean    True if allowed to change the state of the record. Defaults to the permission set in the component.
     * @since    1.6
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        // Check for existing article.
        if (!empty($record->id)) {
            return $user->authorise('core.edit.state', 'com_content.article.' . (int)$record->id);
        } // New article, so check against the category.
        elseif (!empty($record->catid)) {
            return $user->authorise('core.edit.state', 'com_content.category.' . (int)$record->catid);
        } // Default to component settings if neither article nor category known.
        else {
            return parent::canEditState('com_content');
        }
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param    JTable    A JTable object.
     *
     * @return    void
     * @since    1.6
     */
    protected function prepareTable($table)
    {
        // Set the publish date to now
        $db = $this->getDbo();
        if ($table->state == 1 && intval($table->publish_up) == 0) {
            $table->publish_up = JFactory::getDate()->toSql();
        }

        // Increment the content version number.
        $table->version++;

        // Reorder the articles within the category so the new article is first
        if (empty($table->id)) {
            $table->reorder('catid = ' . (int)$table->catid . ' AND state >= 0');
        }
    }

    function removeAllAttach($contentids = null, $folder)
    {
        if ($contentids) {
            if (count($contentids) > 0) {
                //$contentids = implode(',',$contentids);

                $query = 'SELECT * FROM #__tz_pinboard_xref_content'
                    . ' WHERE contentid =' . $contentids;
                $db = JFactory::getDbo();
                $db->setQuery($query);

                if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
                $rows = $db->loadObject();
                if (!empty($rows->attachfiles)) {
                    if (preg_match('/.*\/\/\/.*/i', $rows->attachfiles, $match)) {
                        $attachFiles = explode('///', $rows->attachfiles);

                        foreach ($attachFiles as $item) {
                            $filePath = JPATH_SITE . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $item;
                            if (JFile::exists($filePath)) {
                                JFile::delete($filePath);
                            }
                        }
                    } else {
                        $filePath = JPATH_SITE . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $rows->attachfiles;
                        if (JFile::exists($filePath)) {
                            JFile::delete($filePath);
                        }
                    }
                }
            }
        }
        return true;
    }


    // Show tags
    public function getTags()
    {
        $artid = JRequest::getInt('id', null);
        $db = JFactory::getDbo();
        $tags = null;

        if ($artid) {
            $query = 'SELECT t.* FROM #__tz_pinboard_tags AS t'
                . ' LEFT JOIN #__tz_pinboard_tags_xref AS x ON x.tagsid=t.id'
                . ' WHERE x.contentid=' . $artid;

            $db->setQuery($query);
            if (!$db->query()) {
                var_dump($db->getErrorMsg());
                return false;
            }
            $rows = $db->loadObjectList();

            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $tags[] = trim($row->name);
                }
            }
            if (!empty($tags) && count($tags) > 0)
                $tags = implode(',', $tags);
        }

        return $tags;

    }


    /**
     * Returns a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     *
     * @return    JTable    A database object
     */
    public function getTable($type = 'Tz_Pins', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get a single record.
     *
     * @param    integer    The id of the primary key.
     *
     * @return    mixed    Object on success, false on failure.
     */
    function  selectwebsite($pk)
    {

        $db = JFactory::getDbo();
        $sql = "select url from #__tz_pinboard_website where contentid=$pk";
        $db->setQuery($sql);
        $row = $db->loadObject();

        return $row;
    }

    function Selectboardname($catid)
    {
        $db = JFactory::getDbo();
        $sql = "select c.title as title_c, u.name  as name_u   from #__tz_pinboard_boards as c left join #__users as u
                on c.created_user_id = u.id
                where c.id=$catid";
        $db->setQuery($sql);
        $row = $db->loadObject();

        return $row;
    }

    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {

            // Convert the params field to an array.
            $registry = new JRegistry;
            $registry->loadString($item->attribs);
            $item->attribs = $registry->toArray();

            // Convert the metadata field to an array.
            $registry = new JRegistry;
            $registry->loadString($item->metadata);
            $item->metadata = $registry->toArray();

            // Convert the images field to an array.
            $registry = new JRegistry;
            $registry->loadString($item->images);
            $item->images = $registry->toArray();

            // Convert the urls field to an array.
            $registry = new JRegistry;
            $registry->loadString($item->urls);

            $item->urls = $registry->toArray();

            $ps = (!empty($pk)) ? $pk : (int)$this->getState($this->getName() . '.id');
            $webb = $this->selectwebsite($ps);

            $item->web = $webb;
            $boar = $this->Selectboardname($item->catid);

            $item->board = $boar;

            $item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
        }

        return $item;
    }

    /**
     * Method to get the record form.
     *
     * @param    array $data Data for the form.
     * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    mixed    A JForm object on success, false on failure
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_tz_pinboard.article', 'article', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        $jinput = JFactory::getApplication()->input;

        // The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
        if ($jinput->get('a_id')) {
            $id = $jinput->get('a_id', 0);
        } // The back end uses id so we use that the rest of the time and set it to 0 by default.
        else {
            $id = $jinput->get('id', 0);
        }
        // Determine correct permissions to check.
        if ($this->getState('article.id')) {
            $id = $this->getState('article.id');
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');
            // Existing record. Can only edit own articles in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit.own');
        } else {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        $user = JFactory::getUser();

        // Check for existing article.
        // Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_content.article.' . (int)$id))
            || ($id == 0 && !$user->authorise('core.edit.state', 'com_content'))
        ) {
            // Disable fields for display.
            $form->setFieldAttribute('featured', 'disabled', 'true');
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an article you can edit.
            $form->setFieldAttribute('featured', 'filter', 'unset');
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');

        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     * @since    1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_tz_pinboard.edit.article.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('article.id') == 0) {
                $app = JFactory::getApplication();
                $data->set('catid', JRequest::getInt('catid', $app->getUserState('com_tz_pinboard.articles.filter.category_id')));
            }
        }

        return $data;
    }

    function deleteTags($articleId)
    {
        if (count($articleId) > 0) {
            $articleId = implode(',', $articleId);
            $query = 'DELETE FROM #__tz_pinboard_tags_xref'
                . ' WHERE contentid IN(' . $articleId . ')';
            $db = JFactory::getDbo();
            $db->setQuery($query);
            if (!$db->query()) {
                var_dump($db->getErrorMsg());
                return false;
            }
        }
        return true;
    }

    function deleteImage($artId = null)
    {

        $sizes = array('XS', 'S', 'M', 'L', 'XL');
        $query = 'SELECT * FROM #__tz_pinboard_xref_content'
            . ' WHERE contentid IN(' . implode(',', $artId) . ')';
        $db = JFactory::getDbo();
        $db->setQuery($query);
        if (!$db->query()) {
            echo $db->getErrorMsg();
            return false;
        }

        if ($rows = $db->loadObjectList()) {
            foreach ($rows as $item) {
                $path = null;
                if ($item->images && !empty($item->images)) {
                    if (JFile::getExt($item->images) == "gif") {
                        $path = JPATH_SITE . DIRECTORY_SEPARATOR . $item->images;
                        if (JFile::exists($path)) {
                            JFile::delete($path);
                        }
                    } else {
                        foreach ($sizes as $key => $size) {
                            //Delete Image
                            $str = str_replace('.' . JFile::getExt($item->images),
                                '_' . $size . '.' . JFile::getExt($item->images), $item->images);
                            $str = str_replace('/', DIRECTORY_SEPARATOR, $str);
                            $path = JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $str);

                            if (JFile::exists($path)) {
                                JFile::delete($path);
                            }
                        }
                    }
                }
            }
            return true;
        }
    }

    public function delete($pks)
    {

        if ($pks) {

            $db = JFactory::getDbo();
            $item_arr = implode(",", $pks);
            $sql = 'select tagsid from #__tz_pinboard_tags_xref where contentid IN(' . $item_arr . ')';
            $db->setQuery($sql);
            $row = $db->loadObjectList();
            foreach ($row as $id_tag) {
                $sql = 'delete from #__tz_pinboard_tags where id =' . $id_tag->tagsid . '';
                $db->setQuery($sql);
                $db->query();
            }

            $deleteImage = $this->deleteImage($pks);
            if ($deleteImage == false) {
                $this->setError("COM_TZ_PINBOARD_ERROR_DELETE_IMAGE_LABEL");
                return false;
            }

            foreach ($pks as $item) {
                $this->removeAllAttach($item, 'media');
                $query = 'DELETE FROM #__tz_pinboard_pins'
                    . ' WHERE id = ' . $item;
                $db->setQuery($query);
                if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
                $query1 = 'DELETE FROM #__tz_pinboard_xref_content'
                    . ' WHERE contentid = ' . $item;
                $db->setQuery($query1);
                if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }

                $query2 = 'DELETE FROM #__tz_pinboard_website'
                    . ' WHERE contentid = ' . $item;
                $db->setQuery($query2);
                if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }

                $query3 = 'DELETE FROM #__tz_pinboard'
                    . ' WHERE contentid = ' . $item;
                $db->setQuery($query3);
                if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }

            }

            $this->deleteTags($pks);

            return true;
        }

    }

    function getWeb()
    {
        $db = JFactory::getDbo();

        if (JRequest::getCmd('task') == "article.save2copy") {

            $sql_pin = "select id from #__tz_pinboard_pins order by id desc limit 0,1";
            $db->setQuery($sql_pin);
            $row = $db->loadObject();
            $id = $row->id;

            if (isset($_POST['url_Website']) && !empty($_POST['url_Website'])) {
                $urls = $_POST['url_Website'];
                $sql = "INSERT INTO #__tz_pinboard_website VALUES('NULL', '" . $urls . "',$id,'','')";
                $db->setQuery($sql);
                $db->query();
            }
        } else {
            $id = $this->getState($this->getName() . '.id');
            $urls = "";

            if (isset($_POST['imgurl']) && !empty($_POST['imgurl'])) {
                $urls = $_POST['imgurl'];
            }

            $sql_select = "select * from #__tz_pinboard_website where contentid=$id";
            $db->setQuery($sql_select);
            $row = $db->loadObjectList();
            $count_r = count($row);

            if (empty($count_r)) {

                $sql = "INSERT INTO #__tz_pinboard_website VALUES('NULL', '" . $urls . "',$id,'','')";

            } else {

                $sql = "update  #__tz_pinboard_website set url='" . $urls . "' where  contentid=$id ";
            }
            $db->setQuery($sql);
            $db->query();
        }
    }


    protected function cleanCache($group = null, $client_id = 0)
    {
        parent::cleanCache('com_tz_pinboard');
        parent::cleanCache('mod_tz_pinboard_articles_archive');
        parent::cleanCache('mod_tz_pinboard_articles_categories');
        parent::cleanCache('mod_tz_pinboard_articles_category');
        parent::cleanCache('mod_tz_pinboard_articles_latest');
        parent::cleanCache('mod_tz_pinboard_articles_news');
        parent::cleanCache('mod_tz_pinboard_articles_popular');
    }
}
