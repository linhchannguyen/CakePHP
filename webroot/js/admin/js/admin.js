// -*- mode:javascript; coding:euc-jp-unix -*-
//------------------------------------------------------------------------------
/// @file   admin.js
/// @brief  ���������ġ�����
/// @author Yuichi Nakamura
/// @date   Time-stamp: "2010-02-24 10:54:35"
//------------------------------------------------------------------------------
//------------------------------------------------
/// @brief  ���ꤵ�줿̾���Υ����å��ܥå����Υ����å����֤�����ѹ����� (����̾�б�)
/// @param  cbname      �����å��ܥå���̾
/// @param  is_check    �ѹ���������å��ܥå����ξ���
///         - true      �����å�����
///         - false     �����å���������
/// @return �ʤ�
//------------------------------------------------
function changeCheckboxAll(cbname, is_check)
{
    var elements = document.getElementsByName(cbname);
    for (var i = 0; i < elements.length; i++) {
        elements[i].checked = is_check;
    }
}


//------------------------------------------------
/// @brief  ���ꤵ�줿̾���Υ����å��ܥå����Υ����å����֤����ȿž���� (����̾�б�)
/// @param  cbname      �����å��ܥå���̾
/// @return �ʤ�
//------------------------------------------------
function revertCheckboxAll(cbname)
{
    var elements = document.getElementsByName(cbname);
    for (var i = 0; i < elements.length; i++) {
        elements[i].checked = !elements[i].checked;
    }
}



//------------------------------------------------------------------------------
// end of file
//------------------------------------------------------------------------------
