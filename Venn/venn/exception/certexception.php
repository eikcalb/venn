<?php
namespace Venn\Exception;

/**
 *  This is the exception class for certificate formation and related issues.
 * 
 *  <table>The codes are 3 digits that express information as so:
 *      <tr><td>0xx (initial digit) :</td><td>    shows the level within which the error occurred</td></tr>
 *      <tr><td>x0x (second digit) :</td><td>    shows specific information category for the error</td></tr>
 *      <tr><td>xx0 (last digit) :</td><td>    this is generic and should be application specific</td></tr>
 * </table>
 *  <p>Generally, this exception can be handled by just parsing the first digit: which for instance, may indicate the first stage of certification (key generation)
 * 
 */
class CertException extends Basis{
    
}