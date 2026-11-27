{{-- F7: the Distribution Fuel Request menu badge counts the flows that need admin action, again on every page --}}
@include('distribution_fuel_request.flow_functions')
<script>
    $(function () {
        loadNeedsActionFlows().then(function (flows) {
            updateFlowMenuBadge(flows.length);
        }).catch(function (error) {
            console.error(error);
        });
    });
</script>
