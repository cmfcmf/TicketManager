{include file='Admin/Header.tpl' icon='help' __title='Help'}

<h3>{gt text='Developer'}</h3>

<h4>{gt text='How to use'}</h4>
<p>
	{gt text='To reserve a ticket, you need an unique ticket identification number. Then call the following api-function within your module:'}<br />
	<code>
		$return = ModUtil::apiFunc('TicketManager', 'Ticket', 'reserve', 
			array('number' => 2,
				'eventname' =>'Testevent', 
				'picture' => null, //Absolute path to big picture
				'module' => ModUtil::getIdFromName($this->name),
				'logo' => null, //Absolute path to little logo
				'price' => 4, //Price of a card (currently not printed)
				'information' => array('uid' => 373, 'foo' => 'bar'), //Further information to store
				'startdate' => '15.3.2013 12:00',
				'enddate' => null,
				'shortdescription' => 'Lorem ipsum latinum dolorem sint sant sunt.'
				)
		);

</code><br />

</p>
{include file='Admin/Footer.tpl'}
