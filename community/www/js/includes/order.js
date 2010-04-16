function saveTree(el) {
 parameters = {'node_orders[]': treeObj.getNodeOrders().split(","),
      method: 'post'};
 treeObj.getDeletedUnits().length > 0 ? Object.extend(parameters, {'delete_nodes[]' : treeObj.getDeletedUnits()}) : null;
 treeObj.getActivatedUnits().length > 0 ? Object.extend(parameters, {'activate_nodes[]' : treeObj.getActivatedUnits()}) : null;
 treeObj.getDeactivatedUnits().length > 0 ? Object.extend(parameters, {'deactivate_nodes[]': treeObj.getDeactivatedUnits()}) : null;

 var url = window.location.toString();
 ajaxRequest(el, url, parameters);
}

function repairTree(el) {
 parameters = {repair_tree: true, method: 'post'};
 var url = window.location.toString();
 ajaxRequest(el, url, parameters);
}
