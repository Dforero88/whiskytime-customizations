{*
 * Events Manager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2021 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   eventsmanager
*}

<input type="hidden" name="hidden_seats_table" id="hidden_seats_table">
<div class="panel col-lg-10" style="margin-top: -5px;" id="sections">
        <div class="panel" id="fieldset_0">
            <div class="panel-heading">
                <img src="../img/admin/add.gif" alt="FME Events">{l s='Add Seat Map' mod='eventsmanager'}
            </div>
            <div class="form-wrapper forms">

                <div class="form-group row col-lg-12">
                   <label for="example-text-input" class="col-lg-12 col-form-label">{l s='Enable Seat Selection' mod='eventsmanager'}</label>
                    <div class="col-lg-12">
                         <span class="switch prestashop-switch fixed-width-lg">
                         <input type="radio" name="seat_selection" id="seat_selection_on" value="1" {if isset($seat_selection) AND $seat_selection == 1}checked="checked"{/if}  onclick="show2();">
                         <label for="seat_selection_on">Yes</label>
                         <input type="radio" name="seat_selection" id="seat_selection_off" value="0"  {if isset($seat_selection) AND $seat_selection == 0}checked="checked"{/if} {if ! isset($seat_selection)}checked="checked"{/if} onclick="show1();">
                         <label for="seat_selection_off">No</label>
                         <a class="slide-button btn"></a>
                         </span>
                      </div>
                </div>
                <div class="clearfix"></div>

                <div class="seat_combine col-lg-12" id="seat_combine">
                  <div class="alert alert-info col-lg-12">
                   
                    <p><b>{l s='Better Understanding of Seat Selection Area:' mod='eventsmanager'}</b>{l s=' Kindly follow below steps ' mod='eventsmanager'}</p>
                    <p><b>{l s='1.Enable Seat Selection:' mod='eventsmanager'}</b>{l s=' Enable this option if you want to make a seat map selection for customers' mod='eventsmanager'}</p>
                    <p><b>{l s='2.Number of Columns and Rows:' mod='eventsmanager'}</b>{l s=' Enter the total number of rows and columns of seat map' mod='eventsmanager'}</p>
                    <p><b>{l s='3.Seat Maker:' mod='eventsmanager'}</b>{l s=' Press the seat maker button and the system will generate the seat map' mod='eventsmanager'}</p>
                    <p><b>{l s='4.Disable Seats:' mod='eventsmanager'}</b>{l s=' In seat map if you want to disable any seat then double click on seat and remove the number' mod='eventsmanager'}</p>
                    <p><b>{l s='5.Reserve Seats:' mod='eventsmanager'}</b>{l s=' If you want to reserve any seat for VIP Guests then double click on seat and write "VIP" ' mod='eventsmanager'}</p>
                    <p><b>{l s='6.Edit Seat No:' mod='eventsmanager'}</b>{l s=' You can also edit the seat number. just double click on seat and add number what you want' mod='eventsmanager'}</p>
                    <p><b>{l s='7.Save Seats Data:' mod='eventsmanager'}</b>{l s=' After completion of all changes you must need to press "Save Seats Data" button before SAVE' mod='eventsmanager'}</p>
                    <p>{l s='Once you create the ticket after that you will not be able to modify the seat map.' mod='eventsmanager'}</p>

                </div>


                <div class="form-group row col-lg-4">
                   <label for="example-text-input" class="col-lg-12 col-form-label">{l s='Create seat selection map' mod='eventsmanager'}</label>

                   <div class="col-lg-4">
                    <label for="example-text-input" class="col-form-label">{l s='No of Rows' mod='eventsmanager'}</label>
                  
                    <input class="form-control" min="0" type="number" name="seat_rows" id="seat_rows">
                    </div>
                    <div class="col-lg-4">
                      <label for="example-text-input" class="col-form-label">{l s='Columns' mod='eventsmanager'}</label>
                    <input class="form-control" type="number" min="0" name="seat_col" id="seat_col">
                    </div>

                    <div class="col-lg-8" style="margin-top: 5px;">
                      <button type="button"  onclick="createTable()" class="btn btn-primary" style="width: 100%">{l s='Seat Maker' mod='eventsmanager'}</button>
                    </div>
                </div>

                <div class="form-group row col-lg-8" style="overflow: auto;">
                   <button type="button" id="update_table_button" onclick="updateTable()" class="btn btn-primary fmmflash-button">{l s='Save Seats Data' mod='eventsmanager'}</button>
                   <span class="green_text">{l s='VIP Seats' mod='eventsmanager'}</span>
                   <div class="green_div"></div>
                   <span class="red_text">{l s='Disable Seats' mod='eventsmanager'}</span>
                   <div class="red_div"></div>

                   <table id="row_col_table" border="1"> 
                </table>
                </div>
                </div>


<div class="clearfix"></div>


            </div>
        </div>
         <button id="fme_events_form_submit_btn" class="btn btn-default pull-right" name="submitAddfme_events" value="Save" type="submit"><i class="process-icon-save"></i>{l s='Save' mod='eventsmanager'} </button></div>
 </div>
