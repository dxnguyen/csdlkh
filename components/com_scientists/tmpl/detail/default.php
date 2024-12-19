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
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

$canEdit = Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_scientists.' . $this->item->id);

if (!$canEdit && Factory::getApplication()->getIdentity()->authorise('core.edit.own', 'com_scientists' . $this->item->id))
{
	$canEdit = Factory::getApplication()->getIdentity()->id == $this->item->created_by;
}
?>

<div class="item_fields">
<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
    <?php endif;?>
	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_SCIENTISTS_FORM_LBL_DETAIL_NAME'); ?></th>
			<td><?php echo $this->item->name; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_SCIENTISTS_FORM_LBL_DETAIL_GENDER'); ?></th>
			<td>

			<?php if (!empty($this->item->gender) || $this->item->gender === 0)
			{
					 echo Text::_('COM_SCIENTISTS_SCIENTISTS_GENDER_OPTION_' . strtoupper(str_replace(' ', '_',$this->item->gender)));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_SCIENTISTS_FORM_LBL_DETAIL_BIRTHDAY'); ?></th>
			<td>				<?php
			$date = $this->item->birthday;
			echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC4')) : '-';
			?>

			</td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_SCIENTISTS_FORM_LBL_DETAIL_ADDRESS'); ?></th>
			<td><?php echo $this->item->address; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_SCIENTISTS_FORM_LBL_DETAIL_PHONE'); ?></th>
			<td><?php echo $this->item->phone; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_SCIENTISTS_FORM_LBL_DETAIL_EMAIL'); ?></th>
			<td><?php echo $this->item->email; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_SCIENTISTS_FORM_LBL_DETAIL_POSITION'); ?></th>
			<td><?php echo $this->item->position; ?></td>
		</tr>

	</table>

</div>

<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_scientists.' . $this->item->id) || $this->item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
	<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_scientists&task=detail.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_SCIENTISTS_EDIT_ITEM"); ?></a>
	<?php elseif($canCheckin && $this->item->checked_out > 0) : ?>
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_scientists&task=detail.checkin&id=' . $this->item->id .'&'. Session::getFormToken() .'=1'); ?>"><?php echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php endif; ?>

<?php if (Factory::getApplication()->getIdentity()->authorise('core.delete','com_scientists.detail.'.$this->item->id)) : ?>

	<a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo Text::_("COM_SCIENTISTS_DELETE_ITEM"); ?>
	</a>

	<?php echo HTMLHelper::_(
                                    'bootstrap.renderModal',
                                    'deleteModal',
                                    array(
                                        'title'  => Text::_('COM_SCIENTISTS_DELETE_ITEM'),
                                        'height' => '50%',
                                        'width'  => '20%',
                                        
                                        'modalWidth'  => '50',
                                        'bodyHeight'  => '100',
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_scientists&task=detail.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_SCIENTISTS_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_SCIENTISTS_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>