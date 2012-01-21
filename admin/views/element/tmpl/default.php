<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
?>


<form action="index.php?option=com_pagesanditems&amp;view=element&amp;tmpl=component&amp;object=id" method="post" name="adminForm">

			<table>
				<tr>
					<td width="100%">
						<?php echo JText::_( 'Filter' ); ?>:
						<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
					<td nowrap="nowrap">
						<?php
						echo $this->lists['sectionid'];
						echo $this->lists['catid'];
						?>
					</td>
				</tr>
			</table>

			<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5">
						<?php echo JText::_( 'Num' ); ?>
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort',   'Title', 'c.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</th>
					<th width="7%">
						<?php echo JHTML::_('grid.sort',   'Access', 'groupname', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</th>
					<th width="2%" class="title">
						<?php echo JHTML::_('grid.sort',   'ID', 'c.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</th>
					<th class="title" width="15%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',   'Section', 'section_name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</th>
					<th  class="title" width="15%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',   'Category', 'cc.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</th>
					<th align="center" width="10">
						<?php echo JHTML::_('grid.sort',   'Date', 'c.created', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->page->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
			{
				$row = &$this->rows[$i];

				$link 	= '';
				$date	= JHTML::_('date',  $row->created, JText::_('DATE_FORMAT_LC4') );
				$access	= JHTML::_('grid.access',   $row, $i, $row->state );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $this->page->getRowOffset( $i ); ?>
					</td>
					<td>
						<a style="cursor: pointer;" onclick="window.parent.jSelectArticle('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""),$row->title); ?>', '<?php echo JRequest::getVar('object'); ?>');">
							<?php echo htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8'); ?></a>
					</td>
					<td align="center">
						<?php echo $row->groupname;?>
					</td>
					<td>
						<?php echo $row->id; ?>
					</td>
						<td>
							<?php echo $row->section_name; ?>
						</td>
					<td>
						<?php echo $row->cctitle; ?>
					</td>
					<td nowrap="nowrap">
						<?php echo $date; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		</form>
