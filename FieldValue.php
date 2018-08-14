<?php

namespace WebsolutionsGroup\Firestore;

/**
 * Contains special field values for Cloud Firestore.
 *
 * This class cannot be instantiated, and methods contained within it should be
 * accessed statically.
 */
class FieldValue extends \Google\Cloud\Firestore\FieldValue
{
    /**
     * @access private
     * @codeCoverageIgnore
     */
    private function __construct()
    {
        // Prevent instantiation of this class.
    }

    /**
     * Denotes a field which should be deleted from a Firestore Document.
     *
     * This special value, when used as a field value on update calls, will
     * cause the field to be entirely deleted from Cloud Firestore.
     *
     * Example:
     * ```
     * use Google\Cloud\Firestore\FieldValue;
     * use Google\Cloud\Firestore\FirestoreClient;
     *
     * $firestore = new FirestoreClient;
     * $document = $firestore->document('users/dave');
     * $document->update([
     *     ['path' => 'hometown', 'value' => FieldValue::deleteField()]
     * ]);
     * ```
     *
     * @return string
     */
    public static function deleteField()
    {
        return '___google-cloud-php__deleteField___';
    }

    /**
     * Denotes a field which should be set to the server timestamp.
     *
     * This special value, when used as a field value on create, update or set
     * calls, will cause the field value to be set to the current server
     * timestamp.
     *
     * Example:
     * ```
     * use Google\Cloud\Firestore\FieldValue;
     * use Google\Cloud\Firestore\FirestoreClient;
     *
     * $firestore = new FirestoreClient;
     * $document = $firestore->document('users/dave');
     * $document->update([
     *     ['path' => 'lastLogin', 'value' => FieldValue::serverTimestamp()]
     * ]);
     * ```
     *
     * @return string
     */
    public static function serverTimestamp()
    {
        return '___google-cloud-php__serverTimestamp___';
    }

    /**
     * Check if the given value is a sentinel.
     *
     * @param string $value
     * @return bool
     * @access private
     */
    public static function isSentinelValue($value)
    {
        return in_array($value, [
            self::deleteField(),
            self::serverTimestamp()
        ]);
    }
}