<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Scientists
 * @author     Nguyen Dinh <dxnguyen.ktxhcm@gmail.com>
 * @copyright  2024 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Scientists\Component\Scientists\Site\Helper\ScientistsHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_scientists', JPATH_SITE);

$user    = Factory::getApplication()->getIdentity();
$canEdit = ScientistsHelper::canUserEdit($this->item, $user);


?>

<div class="detail-edit front-end-edit">

<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
    <?php endif;?>
	<?php if (!$canEdit) : ?>
		<h3>
		<?php throw new \Exception(Text::_('COM_SCIENTISTS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_SCIENTISTS_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo Text::_('COM_SCIENTISTS_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-detail"
			  action="<?php echo Route::_('index.php?option=com_scientists&task=detailform.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'detail')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'detail', Text::_('COM_SCIENTISTS_TAB_DETAIL', true)); ?>
	<?php echo $this->form->renderField('name'); ?>

	<?php echo $this->form->renderField('gender'); ?>

	<?php echo $this->form->renderField('birthday'); ?>

	<?php echo $this->form->renderField('address'); ?>

	<?php echo $this->form->renderField('phone'); ?>

	<?php echo $this->form->renderField('email'); ?>

	<?php echo $this->form->renderField('position'); ?>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-danger"
					   href="<?php echo Route::_('index.php?option=com_scientists&task=detailform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
					   <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_scientists"/>
			<input type="hidden" name="task"
				   value="detailform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
