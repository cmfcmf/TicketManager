<head>
	<link rel="stylesheet" type="text/css" href="/modules/TicketManager/style/Ticket.css" />
</head>
<body>
	<div class="ticket">
		<h1 class="title">{$eventname}</h1>
		<table>
			<tr>
				{if $picture != ''}
					<td><img class="picture" src="{$picture}" /></td>
				{/if}
				<td><i>{$shortdescription}</i></td>
				<td><div class="qr">{$qrHtml}</div></td>
			</tr>
		</table>
		<div style="clear: both;" />
		<div class="footer">
			<table>
				<tr>
					{if $startdate != 0}<td><p class="dates">{gt text='Date'}:&nbsp;{$startdate|date_format:"%d.%m.%Y %H:%M"}</p></td>{/if}
					<td class="logo">{img modname='TicketManager' src='admin.png' class="logo"}</td>
					{if $logo != ''}<td class="logo"><img class="logo" src="{$logo}" /></td>{/if}
					{if $price != -1}<td class="price"><p class="price">{$price}€</p></td>{/if}
				</tr>
			</table>
		</div>
	</div>
</body>
