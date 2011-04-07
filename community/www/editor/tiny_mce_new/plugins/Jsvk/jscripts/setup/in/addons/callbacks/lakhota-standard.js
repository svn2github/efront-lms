/**
 * $Id$
 *
 * Lakhota char processor
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright 2008-2009 Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev$
 * @lastchange $Author$ $Date$
 */
function(i,I){if(i=='\u0008'){if(I.length){return[I.slice(0,-1),I.length-1]}}else if(/[^A-z']/.test(i)){return VirtualKeyboard.Langs.LA.remap[I+i]||[I+i,0]}else{return VirtualKeyboard.Langs.LA.remap[I+i]||[I+i,1]}}
