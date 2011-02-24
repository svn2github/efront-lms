<?php
function eF_local_printBranchJobs($branch) {
 $result = $branch -> getJobDescriptions();
 $branchJobs = array("--- {$branch->branch['name']} ---");
 foreach ($result as $value) {
  $branchJobs[$value['description']] = $value['description'];
 }
 $branchJobs['#empty#'] = "--- "._OTHERBRANCHJOBS." ---";
 $result = eF_getTableData("module_hcd_job_description", "distinct description");
 foreach ($result as $value) {
  $branchJobs[$value['description']] = $value['description'];
 }
 return $branchJobs;
}
