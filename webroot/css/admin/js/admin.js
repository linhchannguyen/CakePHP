// -*- mode:javascript; coding:euc-jp-unix -*-
//------------------------------------------------------------------------------
/// @file   admin.js
/// @brief  拠点管理ツール用
/// @author Yuichi Nakamura
/// @date   Time-stamp: "2010-02-24 10:54:35"
//------------------------------------------------------------------------------
//------------------------------------------------
/// @brief  指定された名前のチェックボックスのチェック状態を一括で変更する (配列名対応)
/// @param  cbname      チェックボックス名
/// @param  is_check    変更するチェックボックスの状態
///         - true      チェックする
///         - false     チェックを解除する
/// @return なし
//------------------------------------------------
function changeCheckboxAll(cbname, is_check)
{
    var elements = document.getElementsByName(cbname);
    for (var i = 0; i < elements.length; i++) {
        elements[i].checked = is_check;
    }
}


//------------------------------------------------
/// @brief  指定された名前のチェックボックスのチェック状態を一括で反転する (配列名対応)
/// @param  cbname      チェックボックス名
/// @return なし
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
