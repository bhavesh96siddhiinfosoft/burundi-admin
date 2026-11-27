@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Fuel Requests</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">Fuel Requests</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="date" id="req_filter_from" class="form-control" placeholder="From date">
                    </div>

                    <div class="col-md-3">
                        <input type="date" id="req_filter_to" class="form-control" placeholder="To date">
                    </div>

                    <div class="col-md-3">
                        <select id="req_filter_fuel_type" class="form-control">
                            <option value="">All fuel types</option>
                        </select>
                    </div>

                    <!-- ⭐ NEW SEARCH INPUT ⭐ -->
                    <div class="col-md-3">
                        <input type="text" id="req_filter_search" class="form-control"
                            placeholder="Search by ID / Station / Owner / Status">
                    </div>
                </div>

                <table class="table table-bordered" id="fuel_request_table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fuel Type</th>
                            <th>Quota (L)</th>
                            <th>Station Name</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="fuel_request_list"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var database = firebase.firestore();
var requestsRef = database.collection('fuel_request');
var fuelTypesRef = database.collection('fuel_type');
var usersRef = database.collection('users');

$(document).ready(function () {
    loadFuelTypes();
    loadRequests();

    $('#req_filter_from, #req_filter_to, #req_filter_fuel_type, #req_filter_search')
        .on('change keyup', loadRequests);
});

// Load Fuel Types
function loadFuelTypes() {
    fuelTypesRef.get().then(function(snapshot) {
        let html = '<option value="">All fuel types</option>';
        snapshot.forEach(doc => {
            html += '<option value="' + doc.id + '">' + (doc.data().name || '') + '</option>';
        });
        $('#req_filter_fuel_type').html(html);
    });
}

async function loadRequests() {
    jQuery("#overlay").show();

    const fuelTypeMap = {};
    await fuelTypesRef.get().then(qs => {
        qs.forEach(doc => fuelTypeMap[doc.id] = doc.data().name);
    });

    requestsRef.orderBy('createdAt', 'desc').get().then(async function (snapshots) {
        let allRequests = [];
        let ownerIds = new Set();

        snapshots.forEach(function (doc) {
            let data = doc.data();

            // Apply date + fuel type filters
            if (!requestPassesFilters(data)) return;

            allRequests.push(data);
            if (data.ownerId) ownerIds.add(data.ownerId);
        });

        // Fetch owners
        let userMap = {};
        const userSnapshots = await Promise.all(
            Array.from(ownerIds).map(id => usersRef.doc(id).get())
        );
        userSnapshots.forEach(snap => {
            if (snap.exists) userMap[snap.id] = snap.data();
        });

        // Apply search filter
        const search = $('#req_filter_search').val().trim().toLowerCase();

        if (search !== "") {
            allRequests = allRequests.filter(r => {
                const owner = userMap[r.ownerId] || {};
                return (
                    String(r.id).toLowerCase().includes(search) ||
                    (owner.fuelStationName || "").toLowerCase().includes(search) ||
                    (owner.fullName || "").toLowerCase().includes(search) ||
                    (r.status || "").toLowerCase().includes(search)
                );
            });
        }

        // Build table rows
        let html = '';
        allRequests.forEach(data => {
            const owner = userMap[data.ownerId] || {};
            const fuelTypeName = fuelTypeMap[data.fuelTypeId] || '';
            const date = data.createdAt ? new Date(data.createdAt.toDate()).toLocaleString() : '';

            html += `
                <tr id="rrow_${data.id}">
                    <td>#${data.id}</td>
                    <td>${fuelTypeName}</td>
                    <td>${data.fuelQuota || ''}</td>
                    <td>${owner.fuelStationName || ''}</td>
                    <td>${owner.fullName || ''}</td>
                    <td><span class="badge badge-info">${data.status || ''}</span></td>
                    <td>${date}</td>
                    <td>
                        <a class="btn btn-sm btn-info" href="{{ url('fuel-request/save') }}/${data.id}">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteRequest('${data.id}')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#fuel_request_list').html(html);
        jQuery("#overlay").hide();
    });
}

// Date + Fuel Type filters
function requestPassesFilters(data) {
    var from = $('#req_filter_from').val();
    var to = $('#req_filter_to').val();
    var fuelType = $('#req_filter_fuel_type').val();

    if (fuelType && data.fuelTypeId != fuelType) return false;

    if (from || to) {
        if (!data.createdAt) return false;
        var d = new Date(data.createdAt.toDate());
        if (from && d < new Date(from)) return false;
        if (to && d > new Date(to + 'T23:59:59')) return false;
    }
    return true;
}

function deleteRequest(id) {
    if (!confirm('Are you sure you want to permenently DELETE this fuel request?')) return;

    jQuery("#overlay").show();

    requestsRef.doc(id).delete()
        .then(() => {
            $('#rrow_' + id).remove(); 
            jQuery("#overlay").hide();
        })
        .catch(err => {
            console.error(err);
            jQuery("#overlay").hide();
            alert('Delete failed');
        });
}
</script>
@endsection
