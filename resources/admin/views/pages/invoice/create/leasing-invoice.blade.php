<template x-data="{}" x-if="$store.createInvoiceLeasing.active">
    <div class="dialog" x-data="xInvoice" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>Create Invoice</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#" method="POST">
                        <div class="form-body">
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Customer<span>*</span></label>
                                    <select id="customer_id" name="customer_id" x-model="customer_id"
                                        x-init="fetchSelectCustomer()">
                                        <option value="">Select customer...</option>
                                    </select>
                                    <template x-for="item in dataError?.customer_id">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Project<span>*</span></label>
                                    <select id="project_id" name="project_id" x-init="fetchSelectProject()"
                                        x-model="project_id">
                                        <option value="">Select project...</option>
                                    </select>
                                    <template x-for="item in dataError?.project_id">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Service Type <span>*</span></label>
                                    <select id="type_id" name="type_id" x-model="type_id">
                                        <option value="">Select service type...</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <template x-for="item in dataError?.service_type">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-row">
                                    <label>Select PAC<span>*</span></label>
                                    <input type="text" placeholder="Enter pac number..." x-model="pac_number"
                                        @click="selectPAC()" readonly
                                        :disabled="!project_id || !type_id || !customer_id">
                                    <template x-for="item in dataError?.multiple_po_id">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Invoice Number<span>*</span></label>
                                    <input type="text" placeholder="Enter invoice number..."
                                        x-model="invoice_number">
                                    <template x-for="item in dataError?.invoice_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Exchange Rate<span>*</span></label>
                                    <input type="text" placeholder="Enter exchange rate..." x-model="exchange_rate">
                                    <template x-for="item in dataError?.exchange_rate">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Issue Date<span>*</span> </label>
                                    <input type="text" name="issue_date" id="issue_date" x-ref="issue_date"
                                        autocomplete="off" readonly placeholder="Select issue date">
                                    <template x-for="item in dataError?.issue_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Start Period Date</label>
                                    <input type="text" name="period_start" id="period_start" x-ref="period_start"
                                        autocomplete="off" placeholder="Select start period date" readonly>
                                    <template x-for="item in dataError?.period_start">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>End Period Date</label>
                                    <input type="text" name="period_end" id="period_end" x-ref="period_end"
                                        autocomplete="off" placeholder="Select start period date" readonly>
                                    <template x-for="item in dataError?.period_end">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Charge Type</label>
                                    <select @change="calculatorAmount" name="charge_type" x-model="charge_type">
                                        <option value="">Select type...</option>
                                        <option value="day">Day</option>
                                        <option value="month">Month</option>
                                        <option value="quarter">Quarter</option>
                                    </select>
                                    <template x-for="item in dataError?.charge_type">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Charge Number <span>*</span></label>
                                    <input type="number" name="charge_number" x-model="charge_number"
                                        placeholder="Enter number" min="0" @input="calculatorAmount">
                                    <template x-for="item in dataError?.charge_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Day/Month</label>
                                    <input type="number" name="day_month" x-model="day_month"
                                        placeholder="Enter day / month" min="0" @input="calculatorAmount">
                                    <template x-for="item in dataError?.day_month">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Tax Status <span>*</span></label>
                                    <select name="tax_status" x-model="tax_status" @change="calculatorAmount">
                                        <template x-for="item in taxOptions">
                                            <option :value="item.key"><span x-text="item.text"></span></option>
                                        </template>
                                    </select>
                                    <template x-for="item in dataError?.tax_status">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- New --}}
                            <div class="row" style="margin-top:20px;">
                                <div class="table customTable">
                                    <div class="table-wrapper purchaseInvoice">

                                        {{-- header --}}
                                        <div class="table-header">
                                            <div class="row table-row-3">
                                                <span class="font13">ល.រ</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span class="font13">ប្រភេទ</span>
                                            </div>
                                            <div class="row table-row-20 text-start">
                                                <span class="font13">បរិយាយមុខទំនិញ</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span class="font13">បរិមាណ</span>
                                            </div>
                                            <div class="row table-row-7 text-start">
                                                <span class="font13">ឯកតា</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span class="font13">ថ្លៃឯកតា($)</span>
                                            </div>
                                            <div class="row table-row-10 text-end ">
                                                <span class="font13">អត្រាប្រចាំឆ្នាំ (%)</span>
                                            </div>
                                            <div class="row table-row-10 text-end">
                                                <span class="font13">អត្រាប្រចាំឆ្នាំ(%)</span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span class="font13">ថ្លៃទំនិញ($)</span>
                                            </div>
                                        </div>
                                        <div class="table-header">
                                            <div class="row table-row-3">
                                                <span>No</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span>Item</span>
                                            </div>
                                            <div class="row table-row-20 text-start">
                                                <span>Descriptiont</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>Quality</span>
                                            </div>
                                            <div class="row table-row-7 text-start">
                                                <span>UOM</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>Unit Price($)</span>
                                            </div>
                                            <div class="row table-row-10 text-end ">
                                                <span>Annual Rate (%)</span>
                                            </div>
                                            <div class="row table-row-10 text-end">
                                                <span>Annual Rate (%)</span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span>Amount($)</span>
                                            </div>
                                        </div>

                                        <div class="table-body">

                                            {{-- projectName --}}
                                            <div class="column">
                                                <div class="row table-row-3"></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-20 text-start">
                                                    <span class="label" x-text="project_name ?? '---'"></span>
                                                </div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-7"></div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-10 "></div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-15"></div>
                                            </div>

                                            {{-- body --}}
                                            <template x-for="(pac,index) in pacData">
                                                <div>
                                                    <div style="padding: 10px 0px;font-size:14px;font-weight: 600;"
                                                        x-text="pac._title"></div>
                                                    <template x-for="(item,index) in pac._purchase_detail">
                                                        <div class="column font13">
                                                            <div class="row table-row-3">
                                                                <span x-text="index+1"></span>
                                                            </div>
                                                            <div class="row table-row-15">
                                                                <span x-text="item.service.name"
                                                                    class="bgDisable"></span>
                                                                <input type="hidden" :value="item.service_id"
                                                                    name="service_id[]" />
                                                            </div>
                                                            <div class="row table-row-20 text-start">
                                                                <span>
                                                                    <textarea x-model="item.des"></textarea>
                                                                </span>
                                                            </div>
                                                            <div class="row table-row-10">
                                                                <span>
                                                                    <input type="number" x-model="item.qty"
                                                                        placeholder="qty ..." class="input-table"
                                                                        min="0" step="any"
                                                                        @input="inputChangeType(item,index,'qty')" />
                                                                </span>
                                                            </div>
                                                            <div class="row table-row-7">
                                                                <span>
                                                                    <input type="text" x-model="item.uom"
                                                                        name="uom[]" placeholder="uom ..."
                                                                        class="input-table" />
                                                                </span>
                                                            </div>
                                                            <div class="row table-row-10">
                                                                <span>
                                                                    <input type="number" x-model="item.price"
                                                                        name="price[]" placeholder="price ..."
                                                                        class="input-table" min="0"
                                                                        step="any"
                                                                        @input="inputChangeType(item,index,'price')" />
                                                                </span>
                                                            </div>
                                                            <div class="row table-row-10">
                                                                <span>
                                                                    <input type="number" x-model="item.rate_first"
                                                                        name="first_rate[]" placeholder="rate ..."
                                                                        class="input-table" min="0"
                                                                        step="any"
                                                                        @input="inputChangeType(item,index,'first_rate')" />
                                                                </span>
                                                            </div>
                                                            <div class="row table-row-10">
                                                                <span>
                                                                    <input type="number" name="rate_second[]"
                                                                        placeholder="rate ..." class="input-table"
                                                                        min="0" step="any"
                                                                        x-model="item.rate_second"
                                                                        @input="inputChangeType(item,index,'rate_second')" />
                                                                </span>
                                                            </div>
                                                            <div class="row table-row-15">
                                                                <span>
                                                                    <input type="number" name="amount[]"
                                                                        placeholder="amount ..." class="input-table"
                                                                        x-model="item.amount"
                                                                        @input="inputChangeType(item,index,'amount')"
                                                                        min="0.01" step="0.01" />
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            {{-- footer --}}
                                            <div class="column">
                                                <div class="row table-row-50 left">
                                                    <div class="text font13 invoiceRemark">
                                                        <span>Remark&nbsp;:</span>
                                                        <textarea type="text" x-model="remark"></textarea>
                                                    </div>
                                                    <div class="font13">
                                                        <span>Note: Ref.NBC Exchang Rate 1 USD = </span>
                                                        <span x-text="exchange_rate"></span>
                                                        <span>Riel</span>
                                                    </div>
                                                    <div class="inputTextArea">
                                                        <label class="font13">
                                                            <p>Amount in Word (English & Khmer)</p>
                                                            <span>*</span>
                                                        </label>
                                                        <textarea class="font13" x-model="note"></textarea>
                                                        <template x-for="item in dataError?.note">
                                                            <div class="errorCenter">
                                                                <span class="error" x-text="item"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="row table-row-50 right">
                                                    <div class="column">
                                                        <div class="row table-row-49">
                                                            <div class="div font13">
                                                                <div>សរុប</div>
                                                                <div>Sub total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(sub_total.dollar)"></div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(sub_total.khmer)"></div>
                                                        </div>
                                                    </div>
                                                    <div class="column">
                                                        <div class="row table-row-49">
                                                            <div class="div font13">
                                                                <div>អាករលើតម្លៃបន្ថែម១០%</div>
                                                                <div>VAT10%</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(vat.dollar)">
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(vat.khmer)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="column">
                                                        <div class="row table-row-49">
                                                            <div class="div font13">
                                                                <div>សរុបរួម</div>
                                                                <div>Grand Total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(grand_total.dollar)"></div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(grand_total.khmer)"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="button" class="primary" color="primary" @click="submitFrom()">
                                <i class='bx bx-save'></i>
                                <span>Save & Submit</span>
                            </button>
                            <button type="button" class="close" @click="dialogClose()">
                                <i class='bx bx-x'></i>
                                <span>Close</span>
                            </button>
                        </div>
                    </form>

                    <template x-if="submitLoading">
                        @include('admin::components.spinner')
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    Alpine.data('xInvoice', () => ({
        submitLoading: false,
        sub_total: {
            dollar: 0,
            khmer: 0
        },
        vat: {
            dollar: 0,
            khmer: 0
        },
        grand_total: {
            dollar: 0,
            khmer: 0
        },
        total_child_invoice: {
            sub_total: {
                dollar: 0,
            },
            vat: {
                dollar: 0,
            },
            grand_total: {
                dollar: 0,
            },
            total_qty: 0,
        },
        pac_id: [],
        pac_number: null,
        day_month: null,
        charge_number: null,
        charge_type: null,
        list_purchase_details: [],
        current_date: null,
        numberDay_of_month: 0,
        exchange_rate: @json($rate) ? @json($rate).rate : "",
        total_qty: 0,
        invoice_number: null,
        note: null,
        remark: null,
        dataError: [],
        install_number: null,
        data: null,
        imageLogoSelectOption: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAACXBIWXMAAAsTAAALEwEAmpwYAAAHfklEQVR4nO2daWwUZRiAV6OSKEZ+GaI/9Icxiv/kjyHGpt0pLDPb1nZmKJeCxChNDNUol4LFhAKhKhAvlAqV4oUFyyEI5ZIzHCkUSrulQDl70HOPHtt29jVvoWXbOfabpU2/mf2e5P3T9J1553125vtmZ3bG4WAwGAwGgzJcLteIBJc8hnOLzkRBkmmMhIlyotvtftxhZ+J5cZxTkP50CqKXEySgPZy8WDk+SX7BYTfiUlJGcYJUMNwN5qKLm7aSMmFC+vOcIHooaCzEvJRxyclPcoJ4noKGApPicDg4QczVakTq5Jmw6pu18Pf2XbBzdxE1saVwp30PX/FJ4itOXuweuFHLclaDPxAAGvEHAvYdUzhe/GrgxizJzoFQKAS04icTYk0pOGUM3wg+dQo0NDYBzfg1hKRMetv6UvDEjxOkUPgGfJqVDbTj1xCyctW3kJu3ydpS4vm05wYW/92P68GqQhBLSxnvTntpYOG4QVYWgmzI/92aUuwqxLJS7CzEklKsKiQQaFUL+VotxHJjilWFhEIhcKWk96t70RcrdP/fMlKsKgSZMuP9fnWLU2eBoihgaSlWFrI8Z42qubv37jfMoV6KlYUcPnpC1dhk+S2ovHzVulKsLERRFHg34yNVY93iNNi8ZZvhF6PUSrGyEKT43HkYnzRJs7k46M+anQkZmfM0Y0JyOn1SrC4Ewb1Bp7HRBy+VZmVlPcyERAleQDP4xEcVeEmbCXkAyj2X4MO5iwZNCB49mJBBoKy8ouew+8nCJT1jCF6GfjN9hmHgNSAmhCK0Zl1sDxlGmBDKYEIogwmhDCaEMpgQymBCKIMJoQwmhDKYEMqwlZDOLgWu1XnBc7NRM27W+6Bb0b9x29vaAZW3m3XzzQbWgjXFpJD2YBdsPuyB9XsuGMa2E5c1pVypaYG8vaUR883G5sMV0N7ZHXtCSq/VEzfpep1XlV94vHLQZfRG6bWG2BNSdr2BuEE37vhU+bjnDJUQrC3mhAS7umHrsUsRm7Pr9FVQNH4AhJJ+KRr8QxbWhLXFnBAEx4aaxgBU1bZoRl1zq+GvsVo7uuD6HZ9uvtnAWowmEbYXYgdymRC6YEIogwmhDFsJOemphk37y3RnPL8dLIcLVfWaub62IPxz6uqgnhxiLSc9NbEppKrWS9yoBm+bKr+o+NqQnYdgbTEnpOTqHeIGXaluVuUXHKkYMiFYW8wJwU/9BoLDTf7+i9Da0al5uBsKGXgIbPC1x54QpLoxAEdKb8GBczc049jF29Co0xw8e8fvww6WaOdGE1gLnhyawVZC7EAuE0IXTAhlMCGUYSsh9d62ntkSDt5acbqiBrytQc1c/BK44lYTHC/Tzo0msBasKSaFNPnbIY/gesavB8qgLdilyj9zqXZopr1FpdDs74g9IaZODGtaVPkFRyJf3GInhiaE4Nk3acPqmltV+f+eqRoyIVofANvvIXgl8GjpLcPD1sZ9F6G4sk4zvyXQMeg3OmAtWJOZR0baRkgvihKCYGe3ZigEnensVnTzzQbWYhbbCbE6uUwIXTAhlMGEUAYTQhlMCGUwIWbBq40/bQXIWAYwM8s4MnMAth0ytXgmxCx/7AGQ55mLYg/x4pkQs6zYYF7IFuPnL4bDhJhl+3/mZEyaD+CpIl48E2KWbgWg8BBA1lqA+WuMI/tngOMlphbPhFAGE0IZTAhlMCGUwYRQBhNCGZYW4vcHoL6hMaoND4VCUFNbB+3t5HeEhNMRDEJ1TW3Ur/RrbGqGlhavfYQU7tjd98Di7JWrIRhU39Guh8/nh48XZPXkJonTYd/Bw2CGM8UlkDb5nZ782XPmQm0t+c8NUCC+7CzRLffED+vy+km1pJAWr0/1EpWFny8lkuLz+SFjzrx+udiYSK+X6OXUmbOqZ+tOm5VBJAUb/+Xq71UNx7/1SrGkkJu3qlVFk0jxacgwI0VLBqkUPRkDpVhSiKIozVNnzj5nRorPQAaJFCMZkaREkhEuZd36fMsJwY49GyfLIzlePEIixUcgw0gKiQw9KaQyjF7bSqsQvCF3CQD0vbqBRIrPWEYRx4t+IykGMro4QdphJMVIhpOXyjFIJNEoBK/wvKqVZyRlweKl+jJ48a+4uLhHOLf8up4UnA3pyUgQZAnX7+TFVXpScPanJyOOl0djkEihTchGABhplJuYOP0JjpcOkh4WOEEqGDv2vUd78+N5cRzHSz6S3J73vfPilPD1c4K4nHzdoodLnvzM/dpTn8aXtlhCSObczwpJ801IKQiXYUaKlgxzUvrLIJVCjRDcSDPLIJBSoCWDRIqRDDIp2jJIpAyLEI6Tn3LyotK/GPkDs8vRHVP4u2NGxDq0x5S+MSMSWmNK75gRsXbNMUXsjEtJGeUYDpy8lH2/gVIpSopmOSjFyUt7+hoiSPkkMsKlOAWp7m6uGOB4Mc3M+u/tKaF7+WdJZPTVzsujOUEqvld7yCmIix3DCcenv5zIp73hcrlGPOCiHuJcqa8luFM1Z2eRwE8l5xadZpoZjnNi+ou4HbIsP2Y2F3MwN8Elj4lm3QwGg8FgOAaB/wGnbWqfJWk8KgAAAABJRU5ErkJggg==",
        dataPac: null,
        project_id: null,
        project_name: null,
        customer_id: null,
        type_id: null,
        pacData: [],
        child_invoice: [],
        tax_status: 1,
        taxOptions: @json(config('dummy.tax_status')),
        async init() {
            this.current_date = moment(new Date).format('DD MMM YYYY');
            this.numberDay_of_month = moment(new Date).daysInMonth();
            $("#period_start").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd",
                onSelect: (select) => {
                    $('#period_end').datepicker('option', 'minDate', select)
                }
            });
            $("#period_end").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd",
                onSelect: (select) => {
                    $('#period_start').datepicker('option', 'maxDate', select)
                }
            });
            $("#issue_date").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd",
            });

        },
        selectPAC() {
            SelectOption({
                title: "Select PAC",
                placeholder: "Select PAC",
                multiple: true,
                unselect: true,
                selected: this.pac_id,
                onReady: (callback_data) => {
                    Axios({
                            url: "{{ route('admin-select-pac') }}",
                            params: {
                                project_id: this.project_id,
                                customer_id: this.customer_id,
                                type_id: this.type_id,
                            },
                            method: 'GET'
                        })
                        .then(response => {
                            this.dataPac = response?.data?.data;

                            const data = response?.data?.data?.map(item => {

                                return {
                                    _id: item.id,
                                    _title: item.pac_number,
                                    _image: this.imageLogoSelectOption,
                                    _description: item?.customer?.name_en ?? '',
                                    _purchase_detail: item.purchase_detail,
                                }
                            });
                            callback_data(data);
                        });
                },
                onSearch: (value, callback_data) => {
                    queueSearch = setTimeout(() => {
                        Axios({
                                url: "{{ route('admin-select-pac') }}",
                                params: {
                                    search: value,
                                    project_id: this.project_id,
                                    customer_id: this.customer_id,
                                    type_id: this.type_id,
                                },
                                method: 'GET'
                            })
                            .then(response => {
                                this.dataPac = response?.data?.data;
                                const data = response?.data?.data?.map(
                                    item => {
                                        return {
                                            _id: item.id,
                                            _title: item.pac_number,
                                            _image: this.imageLogoSelectOption,
                                            _description: item?.customer
                                                ?.name_en ?? '',
                                            _purchase_detail: item
                                                .purchase_detail,
                                        }
                                    });
                                callback_data(data);
                            });
                    }, 1000);
                },
                afterClose: (res) => {
                    this.list_purchase_details = [];
                    if (res?.length > 0) {
                        let data = res.map(item => {
                            return {
                                _id: item._id,
                                _title: item._title,
                                _purchase_detail: item._purchase_detail,
                            }
                        });
                        this.pacData = data;
                        this.pac_id = data.map(item => item);
                        this.pac_number = data.map(item => item._title);
                        this.dataPac = this.dataPac.filter(item => {
                            return data.find(data => data._id == item.id);
                        });
                        data.map(item => item).forEach((value) => {
                            value._purchase_detail.forEach((value) => {
                                this.list_purchase_details.push(value);
                            });
                        });
                        this.calculatorAmount();
                    } else {
                        this.pacData= [],
                        this.pac_id = [];
                        this.pac_number = [];
                        this.list_purchase_details = [];
                        this.calculatorAmount();
                    }
                }
            });
        },
        fetchSelectProject() {
            $('#project_id').select2({
                    placeholder: `Select project...`,
                    ajax: {
                        url: '{{ route('admin-select-project') }}',
                        dataType: 'json',
                        type: "GET",
                        quietMillis: 50,
                        data: (param) => {
                            return {
                                search: param.term
                            };
                        },
                        processResults: (data) => {
                            return {
                                results: $.map(data.data, (item) => {
                                    return {
                                        text: item?.name ?? item?.phone,
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                })
                .on('select2:open', (e) => {
                    document.querySelector('.select2-search__field').focus();
                })
                .on('select2:select', (event) => {
                    const selectedData = event.params.data;
                    this.project_id = selectedData.id;
                    this.project_name = selectedData.text;
                    this.pac_id = [];
                    this.pac_number = [];
                    this.list_purchase_details = [];
                });
        },
        fetchSelectCustomer() {
            $('#customer_id').select2({
                placeholder: `Select customer...`,
                ajax: {
                    url: '{{ route('admin-select-customer') }}',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: (param) => {
                        return {
                            search: param.term
                        };
                    },
                    processResults: (data) => {
                        return {
                            results: $.map(data.data, (item) => {
                                return {
                                    text: item?.name_en ? item?.name_en : item?.name_kh,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            }).on('select2:open', (e) => {
                document.querySelector('.select2-search__field').focus();
            }).on('select2:close', async (eventClose) => {
                const _id = eventClose.target.value;
                this.customer_id = _id;
                this.pac_id = [];
                this.pac_number = [];
                this.list_purchase_details = [];
            });
        },
        inputChangeType(item, index, type) {
            this.calculatorAmount(type);
        },
        numberRound(num, decimalPlaces = null) {
            if (!decimalPlaces) {
                return Math.round(num);
            }
            var p = Math.pow(10, decimalPlaces);
            return Math.round(num * p) / p;
        },
        numberFormat(num) {
            return new Intl.NumberFormat().format(num.toFixed(2));
        },
        calculatorAmount(type = null) {
            this.sub_total.dollar = 0;
            this.sub_total.khmer = 0;
            this.vat.dollar = 0;
            this.vat.khmer = 0;
            this.total_qty = 0;
            this.list_purchase_details.forEach((item) => {

                let qty = Number(item.qty);
                if (type != "amount") {
                    if (item.first_rate > 0) {
                        this.rate_calculate = item.first_rate / 100;
                    } else {
                        this.rate_calculate = 1;
                    }
                    if (item.rate_second > 0) {
                        this.second_rate_calculate = item.rate_second / 100;
                    } else {
                        this.second_rate_calculate = 1;
                    }
                    if (this.day_month > 0) {
                        this.numDay = this.day_month;
                    } else {
                        this.numDay = this.numberDay_of_month
                    }
                    if (this.charge_type == 'day') {
                        item.amount = this.numberRound((qty * item.price * 0) + (item.price * qty /
                            this
                            .numDay * this.charge_number), 2);

                    } else if (this.charge_type == 'quarter') {
                        if (this.charge_number == null) {
                            this.charge_number = 4;
                        }
                        item.amount = this.numberRound((item.price * qty * this.rate_calculate *
                                this
                                .second_rate_calculate) / this.charge_number,
                            2);
                    } else if (this.charge_type == 'month' && this.charge_number) {
                        /// if number of charge over than 1 year 
                        item.amount = this.numberRound(item.price * qty * this
                            .rate_calculate * this
                            .second_rate_calculate / 12 * this.charge_number, 2);
                    } else if (this.charge_type == 'month' && !this.charge_number) {
                        item.amount = this.numberRound(item.price * qty * this
                            .rate_calculate * this.second_rate_calculate / 12, 2);
                    } else {
                        if (this.charge_number) {
                            item.amount = this.numberRound(item.price * qty * this
                                .charge_number, 2);
                        } else {
                            item.amount = this.numberRound(item.price * qty, 2);
                        }
                    }
                }
                this.sub_total.dollar += Number(item.amount);
                this.total_qty += item.qty;
            });

            //dollar
            this.sub_total.dollar = this.numberRound(this.sub_total.dollar, 2);
            if (this.tax_status != 2) {
            this.vat.dollar = this.numberRound(Number(this.sub_total.dollar * (10 / 100)), 2);
            }
            this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(this.vat
                .dollar), 2);

            //khmer
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this
                .exchange_rate));
         
            if (this.tax_status != 2) {
                this.sub_total.khmer = this.numberRound(this.grand_total.khmer / 1.1);
                this.vat.khmer = this.numberRound(this.grand_total.khmer - this.sub_total.khmer);
            }else{
                this.sub_total.khmer =  this.grand_total.khmer;
            }
            this.calculatorAmountEachPac(type);
        },
        calculatorAmountEachPac(type = null) {
            this.child_invoice = [];

            this.pacData.forEach(element => {
                this.total_child_invoice = {
                    sub_total: {
                        dollar: 0
                    },
                    vat: {
                        dollar: 0
                    },
                    total_qty: 0,
                    grand_total: {
                        dollar: 0
                    }
                };

                let childInvoice = {};
                childInvoice.purchase_id = element._id;

                element._purchase_detail.forEach((item) => {

                    let qty = Number(item.qty);
                    if (type != "amount") {
                        if (item.first_rate > 0) {
                            this.rate_calculate = item.first_rate / 100;
                        } else {
                            this.rate_calculate = 1;
                        }
                        if (item.rate_second > 0) {
                            this.second_rate_calculate = item.rate_second / 100;
                        } else {
                            this.second_rate_calculate = 1;
                        }
                        if (this.day_month > 0) {
                            this.numDay = this.day_month;
                        } else {
                            this.numDay = this.numberDay_of_month
                        }
                        if (this.charge_type == 'day') {
                            item.amount = this.numberRound((qty * item.price * 0) + (item
                                .price * qty /
                                this
                                .numDay * this.charge_number), 2);

                        } else if (this.charge_type == 'quarter') {
                            if (this.charge_number == null) {
                                this.charge_number = 4;
                            }
                            item.amount = this.numberRound((item.price * qty * this
                                    .rate_calculate *
                                    this
                                    .second_rate_calculate) / this.charge_number,
                                2);
                        } else if (this.charge_type == 'month' && this.charge_number) {
                            /// if number of charge over than 1 year 
                            item.amount = this.numberRound(item.price * qty * this
                                .rate_calculate * this
                                .second_rate_calculate / 12 * this.charge_number, 2);
                        } else if (this.charge_type == 'month' && !this.charge_number) {
                            item.amount = this.numberRound(item.price * qty * this
                                .rate_calculate * this.second_rate_calculate / 12, 2);
                        } else {
                            if (this.charge_number) {
                                item.amount = this.numberRound(item.price * qty * this
                                    .charge_number, 2);
                            } else {
                                item.amount = this.numberRound(item.price * qty, 2);
                            }
                        }
                    }
                    this.total_child_invoice.sub_total.dollar += Number(item.amount);
                    this.total_child_invoice.total_qty += item.qty;
                });

                this.total_child_invoice.sub_total.dollar = this.numberRound(this.total_child_invoice.sub_total.dollar, 2);
                if (this.tax_status != 2) {
                    this.total_child_invoice.vat.dollar = this.numberRound(Number(this.total_child_invoice.sub_total.dollar * (10 / 100)), 2);
                }
                this.total_child_invoice.grand_total.dollar = this.numberRound(Number(this.total_child_invoice.sub_total.dollar) + Number(
                    this.total_child_invoice.vat
                    .dollar), 2);

                childInvoice.total_qty = this.total_child_invoice.total_qty;
                childInvoice.vat = this.total_child_invoice.vat.dollar;
                childInvoice.sub_total = this.total_child_invoice.sub_total.dollar;
                childInvoice.grand_total = this.total_child_invoice.grand_total.dollar;
                this.child_invoice.push(childInvoice);
            });
        },
        jsonParse(data) {
            return JSON.parse(data);
        },
        numberRound(num, decimalPlaces = null) {
            if (!decimalPlaces) {
                return Math.round(num);
            }
            var p = Math.pow(10, decimalPlaces);
            return Math.round(num * p) / p;
        },
        numberFormat(num) {
            return new Intl.NumberFormat().format(num.toFixed(2));
        },
        dateFormat(date) {
            return date ? moment(date).format('DD MMM YYYY') : ' ';
        },
        dateFormatEn(date, type) {
            return date ? moment(date).format(type) : "";
        },
      submitFrom() {
            this.dataError = [];
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: "Are you sure to save?",
                    btnClose: "Close",
                    btnSave: "Yes",
                },
                afterClosed: (result) => {
                    if (result) {
                        this.submitLoading = true;
                        let issue_date = this.$refs.issue_date.value;
                        let period_start = this.$refs.period_start.value;
                        let period_end = this.$refs.period_end.value;
                        let data = {
                            invoice_number: this.invoice_number,
                            po_id: this.pac_id.length > 0 ? this.pac_id[0]._id : null,
                            multiple_po_id: this.pac_id,
                            po_number: this.pac_number,
                            customer_id: this.customer_id,
                            total_price: this.sub_total.dollar,
                            vat: this.vat.dollar,
                            total_grand: this.grand_total.dollar,
                            charge_number: this.charge_number,
                            total_qty: this.total_qty,
                            charge_type: this.charge_type,
                            install_number: null,
                            paid_status: 'Pending',
                            issue_date: issue_date,
                            exchange_rate: this.exchange_rate,
                            invoice_period: null,
                            period_start: period_start,
                            period_end: period_end,
                            note: this.note,
                            remark: this.remark,
                            status: 1,
                            day_month: this.day_month,
                            purchase_details: this.list_purchase_details.length ? JSON
                                .stringify(this.list_purchase_details) : [],
                            check_multiple_pac: true,
                            type_id: null,
                            project_id: this.project_id,
                            service_type: this.type_id,
                            child_invoice: this.child_invoice
                                .length ? JSON
                                .stringify(this.child_invoice) : [],
                            tax_status: this.tax_status,
                            
                        };
                        setTimeout(async () => {
                            await Axios({
                                url: `{{ route('admin-purchase-save-invoice') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: null,
                                    deleteItemID: [],
                                }
                            }).then(async (res) => {
                                let message = res.data.message;
                                if (message == "success") {
                                    this.dialogClose();
                                    location.reload();
                                    Toast({
                                        title: 'Create invoice',
                                        message: 'success',
                                        status: 'success',
                                        size: 'small',
                                    });
                                } else if (message == "dateValid") {
                                    let dateValidErr = moment(
                                        issue_date,
                                        'YYYY MM DD').format(
                                        'YYYY MM');
                                    this.$store.DMCSubmitStatusDialog
                                        .open({
                                            data: {
                                                title: "PAC invoice",
                                                message: `Invoice create isset date <b>${dateValidErr}</b> will disable !`,
                                                btnClose: "Close",
                                                btnSave: "Yes",
                                            }
                                        });
                                }
                            }).catch((e) => {
                                this.dataError = e.response?.data.errors;
                                this.submitLoading = false;
                            }).finally(() => {
                                this.submitLoading = false;
                            });
                        }, 500);
                    }
                }
            });
        },
        dialogClose() {
            this.$store.createInvoiceLeasing.active = false;
        }
    }));
</script>
<script>
    Alpine.store('createInvoiceLeasing', {
        active: false,
        options: {
            data: null,
            selected: null,
            multiple: false,
            title: 'Choose an option',
            placeholder: 'Type to search...',
            allow_close: true,
            onReady: () => {},
            onSearch: () => {},
            beforeClose: () => {},
            afterClose: () => {}
        }
    });
    window.createInvoiceLeasing = (options) => {
        Alpine.store('createInvoiceLeasing', {
            active: true,
            options: {
                ...Alpine.store('createInvoiceLeasing').options,
                ...options
            }
        });
    };
</script>
