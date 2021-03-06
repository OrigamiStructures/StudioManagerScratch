<?php
namespace App\Model\Table;

use App\Model\Entity\Image;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use App\Model\Entity;
use ArrayObject;
use Proffer\Lib\ProfferPath;

/**
 * Images Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\HasMany $Artworks
 * @property \Cake\ORM\Association\HasMany $Formats
 * @property \Cake\ORM\Association\HasMany $Members
 */
class ImagesTable extends AppTable
{

	protected $_new_image = FALSE;

	/**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('images');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Proffer.Proffer', [
            'image_file' => [    // The name of your upload field
                'root' => WWW_ROOT . 'files', // Customise the root upload folder here, or omit to use the default
                'dir' => 'image_dir',   // The name of the field to store the folder
                'thumbnailSizes' => [ // Declare your thumbnails
                    'large' => [   // Define the prefix of your thumbnail
                        'w' => 1500, // Width
                        'h' => 1500, // Height
                        'jpeg_quality'  => 100,
                        'png_compression_level' => 9
                    ],
                    'medium' => [   // Define the prefix of your thumbnail
                        'w' => 750, // Width
                        'h' => 750, // Height
                        'jpeg_quality'  => 100,
                        'png_compression_level' => 9
                    ],
                    'small' => [   // Define the prefix of your thumbnail
                        'w' => 250, // Width
                        'h' => 250, // Height
                        'jpeg_quality'  => 100,
                        'png_compression_level' => 9
                    ],
                ],
                'thumbnailMethod' => 'Gd',  // Options are Imagick, Gd or Gmagick
//                'thumbnailMethod' => 'imagick',   // Options are Imagick, Gd or Gmagick
            ]
        ]);
        $this->belongsTo('Users', [
                'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Artworks', [
                'foreignKey' => 'image_id'
        ]);
        $this->hasMany('Formats', [
                'foreignKey' => 'image_id'
        ]);
        $this->hasMany('Members', [
                'foreignKey' => 'image_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', 'create');

        $validator
            ->allowEmptyString('image');

        $validator
            ->allowEmptyString('image_dir');

        $validator
            ->allowEmptyString('mimetype');

        $validator
            ->allowEmptyString('filesize');

//        $validator
//            ->add('width', 'valid', ['rule' => 'numeric'])
//            ->allowEmpty('width');
//
//        $validator
//            ->add('height', 'valid', ['rule' => 'numeric'])
//            ->allowEmpty('height');

        $validator
            ->allowEmptyString('title');

//        $validator
//            ->allowEmpty('date');

        $validator
            ->allowEmptyString('alt');

//        $validator
//            ->add('upload', 'valid', ['rule' => 'numeric'])
//            ->allowEmpty('upload');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
//        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }

	/**
	 * If an override image is being uploaded, remember it's name for afterSave processing
	 *
	 * @param Event $event
	 * @param type $entity
	 * @param ArrayObject $options
	 * @return boolean
	 */
	public function beforeSave(Event $event, $entity, ArrayObject $options) {
		$this->_new_image = FALSE;
		if ($entity->dirty()) {
			$this->_new_image = $entity->image_file;
		}
		if ($entity->isNew()) {
			$entity->user_id = $this->contextUser()->artistId();
			$entity->dirty('user_id');
		}
		return true;
	}

	/**
	 * Delete previous image if an override image was uploaded
	 *
	 * @param Event $event
	 * @param type $entity
	 * @param ArrayObject $options
	 * @return boolean
	 */
	public function afterSave(Event $event, $entity, ArrayObject $options) {
		if ($this->_new_image) {
			$settings = $this->behaviors()->get('Proffer')->config('image_file');
			$path = new ProfferPath($this, $entity, 'image_file', $settings);
			$folder = $path->getFolder();
			$base_image = $folder . $this->_new_image;

			$collection = new \Cake\Collection\Collection(glob($folder . "*"));
			$collection->each(function($value) use ($base_image) {
				if (!stristr($value, $this->_new_image) || $value === $base_image) {
					unlink($value);
				}
			});
			$this->_new_image = FALSE;
		}
		return true;
	}

}
