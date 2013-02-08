{include file='Admin/Header.tpl' icon='' __title='Generate tickets'}

{form cssClass='z-form'}
    {formvalidationsummary}
    <fieldset>
        <legend>{gt text='Basic information'}</legend>
        <div class="z-formrow">
            {formlabel __text='Eventname' for='eventname' mandatorysym=true}
            {formtextinput id='eventname' maxLength='100' text='' mandatory=true readOnly=false}
        </div>
        <div class="z-formrow">
            {formlabel __text='Shortdescription' for='shortdescription'}
            {formtextinput id='shortdescription' size=1 text='' textMode='multiline' readOnly=false}
        </div>
        <div class="z-formrow">
            {formlabel __text='Picture' for='picture'}
            {formuploadinput id='picture'}
            <em class="z-sub z-formnote">{gt text='This picture will be printed onto each ticket.'}</em>
        </div>
        <div class="z-formrow">
            {formlabel __text='Logo' for='logo'}
            {formuploadinput id='logo'}
            <em class="z-sub z-formnote">{gt text='This logo will be printed ontop of each DINA4 ticket page.'}</em>
        </div>
        <div class="z-formrow">
            {formlabel __text='Price' for='price'}
            {formtextinput id='price' maxLength='5' text=''}
            <em class="z-sub z-formnote">{gt text='Examples: 4€; $50; 8,99€;'}</em>
        </div>
        <div class="z-formrow">
            {formlabel __text='Eventdate' for='eventdate'}
            {formdateinput id='eventdate' includeTime=true}
            <em class="z-sub z-formnote">{gt text='The eventdate to print onto each ticket.'}</em>
        </div>
    </fieldset>
    <fieldset>
        <legend>{gt text='Misc'}</legend>
        <div class="z-formrow">
            {formlabel __text='Amount of tickets' for='amount' mandatorysym=true}
            {formintinput id='number' mandatory=true text=1}
        </div>
        <div class="z-formrow">
            {formlabel __text='Allowed depreciatings of each ticket' for='allowedDepreciatings' mandatorysym=true}
            {formintinput id='allowedDepreciatings' mandatory=true text=1}
            <em class="z-sub z-formnote">{gt text='-1 means endless, 0 is not allowed.'}</em>
        </div>
        <div class="z-formrow">
            {formlabel __text='Startdate' for='startdate'}
            {formdateinput id='startdate' includeTime=true}
            <em class="z-sub z-formnote">{gt text='Since when the ticket can be depreciated'}</em>
        </div>
        <div class="z-formrow">
            {formlabel __text='Enddate' for='enddate'}
            {formdateinput id='enddate' includeTime=true}
            <em class="z-sub z-formnote">{gt text='Until when the ticket can be depreciated'}</em>
        </div>
    </fieldset>

    <fieldset>
        <legend>{gt text='Download'}</legend>
        <div class="z-formrow">
            {formlabel __text='Direct download' for='direct'}
            {formradiobutton id='direct' dataField='pdfOutput' value='direct' checked=true mandatory=true} <!--todo: "checked" does not work!-->
        </div>
        <div class="z-formrow">
            {formlabel __text='Save at ztemp' for='save'}
            {formradiobutton id='save' dataField='pdfOutput' value='file' mandatory=true} 
        </div>
    </fieldset>

    <div class="z-buttons z-formbuttons">
        {formbutton commandName='generate' __text='Generate' class='z-bt-ok'}
        {formbutton commandName='cancel' __text='Cancel' class='z-bt-cancel'}
    </div>

{/form}

{include file='Admin/Footer.tpl'}
