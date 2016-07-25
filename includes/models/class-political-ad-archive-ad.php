<?php

/**
 * The candidate model
 *
 * @since      1.0.0
 * @package    PoliticalAdArchive
 * @subpackage PoliticalAdArchive/includes/models
 * @author     Daniel Schultz <dan.schultz@archive.org>
 */
class PoliticalAdArchiveAd {
	private $wp_id;
	private $archive_id;
	private $embed_url;
	private $notes;
        private $sponsors;
        private $sponsor_names;
	private $subjects;
        private $candidates;
        private $candidate_names;
	private $type;
	private $race;
	private $cycle;
	private $message;
	private $air_count;
	private $reference_count;
	private $market_count;
	private $transcript;
	private $date_ingested;

	public function PoliticalAdArchiveAd($wp_id) {
                $post_metadata = get_fields($wp_id);
                $this->wp_id = $wp_id;
                $this->embed_url = array_key_exists('embed_url', $post_metadata)?$post_metadata['embed_url']:'';
                $this->notes = array_key_exists('notes', $post_metadata)?$post_metadata['notes']:'';
                $this->archive_id = array_key_exists('archive_id', $post_metadata)?$post_metadata['archive_id']:'';
                $ad_sponsors_acf_value = array_key_exists('ad_sponsors', $post_metadata)?$post_metadata['ad_sponsors']:array();
                $ad_sponsors_acf_value = $ad_sponsors_acf_value?$ad_sponsors_acf_value:array();
                $this->sponsors = PoliticalAdArchiveSponsor::get_sponsors_by_acf_field_value($ad_sponsors_acf_value);
                $this->sponsor_names = array_map(function($x) { return $x->name; }, $this->sponsors);
                $this->sponsor_types = array_map(function($x) { return $x->type; }, $this->sponsors);
                $this->sponsor_affiliations = array_map(function($x) { return $x->affiliation; }, $this->sponsors);
                $this->sponsor_affiliation_types = array_map(function($x) { return $x->affiliation_type; }, $this->sponsors);
                $ad_candidates_acf_value = array_key_exists('ad_candidates', $post_metadata)?$post_metadata['ad_candidates']:array();
                $ad_candidates_acf_value = $ad_candidates_acf_value?$ad_candidates_acf_value:array();
                $this->candidates = PoliticalAdArchiveCandidate::get_candidates_by_acf_field_value($ad_candidates_acf_value);
                $this->candidate_names = array_map(function($x) { return $x->name; }, $this->candidates);
                $ad_subjects_acf_value = array_key_exists('ad_subjects', $post_metadata)?$post_metadata['ad_subjects']:array();
                $ad_subjects_acf_value = $ad_subjects_acf_value?$ad_subjects_acf_value:array();
                $this->subjects = array_map(function($x) { return $x['ad_subject']; }, $ad_subjects_acf_value);
                $this->type = array_key_exists('ad_type', $post_metadata)?$post_metadata['ad_type']:'';
                $this->race = array_pop(array_merge(
                        array_map(function($x) { return $x->race; }, $this->sponsors),
                        array_map(function($x) { return $x->race; }, $this->candidates)
                ));
                $this->cycle = array_pop(array_merge(
                        array_map(function($x) { return $x->cycle; }, $this->sponsors),
                        array_map(function($x) { return $x->cycle; }, $this->candidates)
                ));
                $this->message = array_key_exists('ad_message', $post_metadata)?$post_metadata['ad_message']:'';
                $this->air_count = array_key_exists('air_count', $post_metadata)?$post_metadata['air_count']:'';
                $this->market_count = array_key_exists('market_count', $post_metadata)?$post_metadata['market_count']:'';
                $this->first_seen = array_key_exists('first_seen', $post_metadata)?$post_metadata['first_seen'].' UTC':'';
                $this->last_seen =array_key_exists('last_seen', $post_metadata)? $post_metadata['last_seen'].' UTC':'';
                $this->transcript =array_key_exists('transcript', $post_metadata)? $post_metadata['transcript']:'';
                $this->date_ingested = $row->post_date.' UTC';
	}
	
	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}
}