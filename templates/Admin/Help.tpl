{include file='Admin/Header.tpl' icon='help' __title='Help'}

<h3>{gt text='Developer'}</h3>

<h4>{gt text='How to use'}</h4>
<p>
	{gt text='To reserve a ticket, you need an unique ticket identification number. Then call the following api-function within your module:'}<br />
	<code>$returnval = ModUtil::apiFunc('TicketManager', 'Ticket', 'reserve', array('id' => $yourId));</code><br />
	{gt text='Now check the returned value:'}<br />
	<code>
		$returnval['pdf']; //Includes a PDF file in DINA4 format with 4 tickets per page.
	</code><br />
</p>
{include file='Admin/Footer.tpl'}
