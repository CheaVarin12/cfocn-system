<dialog id="myDialog">
    <div class="form-admin" id="my-section">form
        <form id="form" action="{!! url()->current() !!}" method="GET" class="form-wrapper" action="#"
            style="padding: 0px;">
            <div class="form-body" style="padding: 10px;box-shadow: none;">
                <input type="hidden" name="check" x-model="formData.check" value="{!! request('check') !!}">
                <div class="row" style="margin-bottom: 10px;">
                    <div class="form-row row-search">
                        <label>Search</label>
                        <input type="search" name="search" x-model="formData.search" value="{!! request('search') !!}" placeholder="Enter keyword search">
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row row-search">
                        <label>Pos Speed</label>
                        <input type="text" x-model="selectedPosSpeed" placeholder="Pos Speed" readonly
                            @click="showHideDropdown('pos_speed_id')">
                        <div class="dropdown" x-show="showPosSpeedDropdown">
                            <ul>
                                <!-- Select All Option -->
                                <li class="fttx-li">
                                    <input type="checkbox" @click="toggleSelectAllPosSpeed($event)"
                                        :checked="formData.pos_speed_id.length === posSpeed.length">
                                    <span>Select All</span>
                                </li>

                                <!-- Loop through each item -->
                                @foreach ($posSpeed as $value)
                                    <li class="fttx-li">
                                        <input type="checkbox" x-model="formData.pos_speed_id"
                                            value="{{ $value->id }}">
                                        <span>{{ $value->split_pos }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" name="pos_speed_id" x-model="formData.pos_speed_id">

                    <!-- Status Dropdown -->
                    <div class="form-row row-search">
                        <label>Status</label>
                        <input type="text" x-model="selectedStatus" placeholder="Status" readonly
                            @click="showHideDropdown('status')">
                        <div class="dropdown" x-show="showStatusDropdown">
                            <ul>
                                <!-- Select All Option -->
                                <li class="fttx-li">
                                    <input type="checkbox" @click="toggleSelectAllStatus($event)"
                                        :checked="formData.status.length === statusOptions.length">
                                    <span>Select All</span>
                                </li>

                                <!-- Loop through each status -->
                                @foreach (config('dummy.fttx_status') as $status)
                                    <li class="fttx-li">
                                        <input type="checkbox" x-model="formData.status" value="{{ $status['key'] }}">
                                        <span>{{ $status['text'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" name="status" x-model="formData.status">

                    <div class="form-row row-search">
                        <label>Start Completed Date</label>
                        <input type="date" id="startNewInstallDate" name="start_completed_date"
                            x-model="formData.start_completed_date" value="{!! request('start_completed_date') !!}">
                    </div>
                    <div class="form-row row-search">
                        <label>End Completed Date</label>
                        <input type="date" id="endNewInstallDate" name="end_completed_date"
                            x-model="formData.end_completed_date" value="{!! request('end_completed_date') !!}">
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row row-search">
                        <label>Start Dismantle Date</label>
                        <input type="date" id="startDismantleDate" name="start_dismantle_date"
                            x-model="formData.start_dismantle_date" value="{!! request('start_dismantle_date') !!}">
                    </div>
                    <div class="form-row row-search">
                        <label>End Dismantle Date</label>
                        <input type="date" id="endDismantleDate" name="end_dismantle_date"
                            x-model="formData.end_dismantle_date" value="{!! request('end_dismantle_date') !!}">
                    </div>
                    <div class="form-row row-search">
                        <label>Start Payment Date</label>
                        <input type="date" id="startPaymentDate" name="start_payment_date"
                            x-model="formData.start_payment_date" value="{!! request('start_payment_date') !!}">
                    </div>
                    <div class="form-row row-search">
                        <label>End Payment Date</label>
                        <input type="date" id="endPaymentDate" name="end_payment_date"
                            x-model="formData.end_payment_date" value="{!! request('end_payment_date') !!}">
                    </div>
                    <div class="form-row row-search">
                        <label>Start Deadline Date</label>
                        <input type="date" id="startDeadlineDate" name="start_deadline_date"
                            x-model="formData.start_deadline_date" value="{!! request('start_deadline_date') !!}">
                    </div>
                    <div class="form-row row-search">
                        <label>End Deadline Date</label>
                        <input type="date" id="endDeadlineDate" name="end_deadline_date"
                            x-model="formData.end_deadline_date" value="{!! request('end_deadline_date') !!}">
                    </div>
                    <input type="hidden" name="expire" x-model="formData.expire">
                    <input type="hidden" name="check_renewal_all" x-model="formData.check_renewal_all">
                </div>
                <div class="footer-button">
                    <button @click="showDialogHideSearch('hide')" type="button" class="close-button"
                        autofocus>Close</button>
                    <button @click="search()" type="submit" class="submit-button" style="margin-left: 8px;"
                        autofocus>Search</button>
                </div>
            </div>
        </form>
    </div>

</dialog>
