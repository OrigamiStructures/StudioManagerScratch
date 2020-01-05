<?php
//Member Types for Member/Group
define('MEMBER_TYPE_ORGANIZATION', 'Organization');
define('MEMBER_TYPE_PERSON', 'Person');
define('MEMBER_TYPE_USER', 'User');
define('MEMBER_TYPE_CATEGORY', 'Category');

// <editor-fold defaultstate="collapsed" desc="EDITION TYPES">
define('EDITION_UNIQUE', 'Unique');
define('EDITION_RIGHTS', 'Rights');
define('EDITION_LIMITED', 'Limited Edition');
define('EDITION_OPEN', 'Open Edition');
define('PORTFOLIO_LIMITED', 'Limited Portfolio');
define('PORTFOLIO_OPEN', 'Open Portfolio');
define('PUBLICATION_LIMITED', 'Limited Publication');
define('PUBLICATION_OPEN', 'Open Publication');
// </editor-fold>

// Serves as a boolean argument in method call(s)
define('NUMBERED_PIECES', 1);
define('OPEN_PIECES', 0);

// These will need to be change to something meaningful
// For now, we can act as admins even though we're users
define('ADMIN_SYSTEM', 'user'); // 'admin'
define('ADMIN_ARTIST', 'artist_admin');

//System Constants
// Serves as a boolean argument in method call(s)
define('SYSTEM_VOID_REFERER'	, TRUE);
define('SYSTEM_CONSUME_REFERER' , FALSE);

//AssignemtTrait Constants
// boolean argument to control the kind of return value from a method
define('PIECE_ENTITY_RETURN'	, FALSE);
define('PIECE_COLLECTION_RETURN', TRUE);

// <editor-fold defaultstate="collapsed" desc="PIECE FILTERS">

// NOTES ON ADDING TO THIS SECTION
// PieceTableHelper::_map needs matching entry to identify the
// filter strategy callable or sort strategy callable
define('PIECE_FILTER_LOAN_FOR_RANGE', 'for_loan_in_range');
define('PIECE_FILTER_FOR_SALE_ON_DATE', 'for_sale_on_date');
define('PIECE_FILTER_COLLECTED', 'collected');
define('PIECE_FILTER_NOT_COLLECTED', 'not_collected');
define('PIECE_FILTER_ASSIGNED', 'assigned');
define('PIECE_FILTER_UNASSIGNED', 'not_assigned');
define('PIECE_FILTER_FLUID', 'fluid');
define('PIECE_FILTER_RIGHTS', 'rights');
define('PIECE_FILTER_NONE', 'none');
define('PIECE_SORT_NONE', 'none');
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="DISPOSITION TYPES">
define('DISPOSITION_TRANSFER', 'transfer');
define('DISPOSITION_LOAN', 'loan');
define('DISPOSITION_STORE', 'storage');
define('DISPOSITION_UNAVAILABLE', 'unavailable');
//define('DISPOSITION_REVIEW'				, 'review');


define('DISPOSITION_TRANSFER_SALE', 'Sale');
define('DISPOSITION_TRANSFER_SUBSCRIPTION', 'Subscription');
define('DISPOSITION_TRANSFER_DONATION', 'Donation');
define('DISPOSITION_TRANSFER_GIFT', 'Gift');
define('DISPOSITION_TRANSFER_RIGHTS', 'Published');
//
define('DISPOSITION_LOAN_SHOW', 'Show');
define('DISPOSITION_LOAN_CONSIGNMENT', 'Consignment');
define('DISPOSITION_LOAN_PRIVATE', 'Loan');
define('DISPOSITION_LOAN_RENTAL', 'Rental');
define('DISPOSITION_LOAN_RIGHTS', 'Licensed');
define('DISPOSITION_REVIEW_CONTACT', 'Contact');
//
define('DISPOSITION_UNAVAILABLE_LOST', 'Lost');
define('DISPOSITION_UNAVAILABLE_DAMAGED', 'Damaged');
define('DISPOSITION_UNAVAILABLE_STOLEN', 'Stolen');
define('DISPOSITION_NFS', 'Not For Sale');
//
define('DISPOSITION_STORE_STORAGE', 'Storage');
// </editor-fold>


define('PIECE_SPLIT_RETURN_NEW', 'new');
define('PIECE_SPLIT_RETURN_BOTH', 'both');

// <editor-fold defaultstate="collapsed" desc="FONT ICONS">
define('ICON_REVIEW', 'fi eye');
define('ICON_REFINE', 'fi pencil');
define('ICON_REMOVE', 'fi trash');
define('ICON_WRENCH', 'fi wrench');
define('ICON_COG', 'fi cog');

define('ICON_MEMBER_TYPE_INSTITUTION', 'fi-results-demographics');
define('ICON_MEMBER_TYPE_PERSON', 'fi-torsos-female-male');
define('ICON_MEMBER_TYPE_USER', 'fi-torsos-all');
define('ICON_MEMBER_TYPE_CATEGORY', 'fi-results');
// </editor-fold>

define('REJECTION_RECORD', TRUE);
define('REJECTION_DONT_RECORD', FALSE);

define('CLEAR', TRUE);

// <editor-fold defaultstate="collapsed" desc="TEMPLATE LAYERS">
/**
 * Template layer element names are sent to the View in a
 * simple array. To make the elements easy to access without
 * resorting to an associative array, I'm using constants
 * to stand in for the index numbers.
 */
define('WRAPPER_LAYER', 0);
define('ARTWORK_LAYER', 1);
define('EDITION_LAYER', 2);
define('FORMAT_LAYER', 3);
// </editor-fold>
