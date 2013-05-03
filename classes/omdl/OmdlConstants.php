<?php
interface OmdlConstants{
	
    const DEFAULT_STATUS = ""; // 
    const DEFAULT_DESCRIPTION = ""; // 

    // Vertical alignment properties
    const POSITION_TOP = "TOP";// use for vertical alignment
    const POSITION_MIDDLE = "MIDDLE"; // use for vertical alignment
    const POSITION_BOTTOM = "BOTTOM";// use for vertical alignment

    // Horizontal alignment properties
    const POSITION_LEFT = "LEFT"; // use for horizontal alignment
    const POSITION_CENTER = "CENTER"; // use for horizontal alignment
    const POSITION_RIGHT = "RIGHT"; // use for horizontal alignment

    // Default Layouts
    const GRID_LAYOUT = "GRID";
    const FLOW_LAYOUT = "FLOW";

    // Layout properties
    const COLUMNS = "COLUMN";
    const ROWS = "ROWS";

    // Width properties
    const WIDE = "WIDE";
    const NARROW = "NARROW";

    // Column numbers - currently only support up to 4
    const NUMBER_ONE = "ONE";
    const NUMBER_TWO = "TWO";
    const NUMBER_THREE = "THREE";
    const NUMBER_FOUR = "FOUR";

    const SPACE = " ";

    const MOODLE_APP_TYPE_W3C = "application/widget";
    const MOODLE_APP_TYPE_OPENSOCIAL = "application/vnd-opensocial+xml";
    const MOODLE_APP_TYPE_UNKNOWN = "application/unknown";
    const REL_TYPE = "source";
    const APP_TYPE_OPENSOCIAL = "OpenSocial";
    const APP_TYPE_W3C = "W3C";

    // XML Element and attributes
    const aNAMESPACE = "http://omdl.org";
    const WORKSPACE = "workspace";
    const aTITLE = "title";
    const LAYOUT = "layout";
    const APP = "app";
    const LINK = "link";
    const POSITION = "position";
    const ID_ATTRIBUTE = "id";
    const TYPE_ATTRIBUTE = "type";
    const HREF = "href";
    
    const aSTATUS = "status";
    const aDATE = "date";
    const aIDENTIFIER = "identifier";
    const aCREATOR = "creator";
    const aREL = "rel";

    const DEFAULT_LAYOUT = FLOW_LAYOUT;

    const UNKNOWN_VALUE = "unknown";
}
?>