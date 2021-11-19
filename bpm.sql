SELECT bs2.id as parent_id, bs2.name as parent_status, bs.id as status_id, bs.name as status, bs1.id as child_id, bs1.name as child_status
FROM `business_process_management` bpm 
INNER JOIN booking_status bs ON bpm.status_id = bs.id 
INNER JOIN booking_status bs1 ON bpm.child_id = bs1.id
INNER JOIN booking_status bs2 ON bpm.parent_id = bs2.id
